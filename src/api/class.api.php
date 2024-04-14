<?php

use \AuditCongress\Members;

use \AuditCongress\Congresses;
use \AuditCongress\Sessions;

use \AuditCongress\Enviroment;


require_once "api.cache.php";
require_once "class.api.route.validator.php";


class API {
    /*
        Generic API functions to complete the response
    */
    private static function Return($data) {
        header('Content-Type: application/json');
        print_r(json_encode($data));
    }
    public static function Success($data) {
        $data["request"]["status"] = "Success";
        API::Return($data);
    }
    public static function Error($error) {
        $data = ["request" => array()];
        $data["request"]["status"] = "error";
        $data["request"]["message"] = $error;
        API::Return($data);
    }
    public static function NotFound($route) {
        $data = ["request" => array()];
        $data["request"]["status"] = "not found";
        if (strlen($route) > 0) $data["request"]["message"] = "Unknown route: $route";
        else $data["request"]["message"] = "No route provided";
        API::Return($data);
    }

    /*
        Helper functions
    */
    //Get a the given query arg fromm $_GET or null
    public static function getQueryArgIfSet($arg) {
        if (isset($_GET[$arg])) return $_GET[$arg];
        else return null;
    }
    //Get the API data, either via fetch or cache
    private static function getAPIData($object) {
        $data = APICache::UseCache($object);
        return $data;
    }
    //Do a full API response for the given route and function(...$args)
    private static function doAPIResponse($slug, $object) {
        try {
            $data = API::getAPIData($object);
            API::Success($data);
        } catch (Exception $ex) {
            API::Error($ex->getMessage());
        }
    }

    /*
        BILL ROUTES
    */
    //Handle all individual bill routes
    //Up to one or no api calls will be made if cached
    public static function HandleBillRoute() {
        $congress = API::getQueryArgIfSet("congress");
        //These have to be lowercase for the API
        $type = API::getQueryArgIfSet("type");
        $number = API::getQueryArgIfSet("number");
        $option = API::getQueryArgIfSet("option");

        $object = null; $args = [$congress, $type, $number, $option];
        
        if (APIRouteValidator::shouldFetchBillsByCongress(...$args))       $object = new \CongressGov\BillList($congress);
        else if (APIRouteValidator::shouldFetchBillsByCongressByType(...$args)) $object = new \CongressGov\BillList($congress, $type);
        else if (APIRouteValidator::couldFetchBillOrOption(...$args)) {
            $bill = new \CongressGov\Bill($congress, $type, $number);
            if (APIRouteValidator::shouldFetchBill(...$args)) $object = $bill;
            else $object = $bill->getOption($option);
        }

        if ($object == null) API::NotFound("bill/$congress/$type/$number/$option");
        else API::doAPIResponse("bill", $object, $args);
    }
    //Handle the logic for getting all bill api data
    private static function getFullBillData($args) {
        $start = time();
    
        $bill = new \CongressGov\Bill(...$args);
        $billData = API::getAPIData($bill);
        
        $options = \CongressGov\Bill::getOptionList();
        //Get data for each option
        foreach ($options as $option) {
            $optionData = API::getAPIData($bill->getOption($option));

            //Some bill options / subroutes have different keys
            $optionIndex = $option; $billIndex = $option;
            if ($option == "relatedbills") $billIndex = $optionIndex = "relatedBills";
            if ($option == "text") { $billIndex = "textVersions"; $optionIndex = "texts"; }
            if ($option == "subjects") { $billIndex = "subjects"; $optionIndex = "legislativeSubjects"; }
            
            $billData[$billIndex] = $optionData[$optionIndex];
        }   

        //If the bill has sponsors, get more detailed info about them
        if (isset($billData["sponsors"])) {
            $sponsors = $billData["sponsors"];
            for ($i = 0;$i < count($sponsors);$i++) {
                $member = new \CongressGov\Member($sponsors[$i]["bioguideId"]);
                $sponsors[$i] = API::getAPIData($member);
            }
            $billData["sponsors"] = $sponsors;
        }

        $data = array();
        $data["bill"] = $billData;
        $data["request"]["dataType"] = "full";
        $data["request"]["time"] = (time()-$start);
        return $data;
    }
    //Handle asking for all bill data in one response
    public static function HandleFullBillRoute() {
        $congress = API::getQueryArgIfSet("congress");
        $type = API::getQueryArgIfSet("type");
        $number = API::getQueryArgIfSet("number");

        $args = [$congress, $type, $number, null];
        //Ensure required args are present
        if (APIRouteValidator::shouldFetchFullBill(...$args)) {
            try {
                $data = API::getFullBillData($args);
                API::Success($data);
            } catch (Exception $e) {
                API::Error($e->getMessage());
            }
        } else {
            //This route doesnt exist
            API::NotFound("fullBill/$congress/$type/$number");
        }
    }
    //Handle asking for recent bills
    public static function HandleRecentBillsRoute() {
        $page = API::getQueryArgIfSet("page");
        if (!isset($page)) $page = 1;
        $pageSize = 25;
        $list = null;
        if (APIRouteValidator::shouldFetchRecentBillsPage($page)) 
            $list = new \CongressGov\BillList(null, null, $pageSize, ($page-1)*$pageSize);
        $sort = $list == null ? "" : $list->getSortType();

        API::doAPIResponse("recent.bills", $list, [$pageSize, $page, $sort]);
    }



    /*
        MEMBER ROUTES
    */
    public static function getMemberData($route, $bioguideId) {
        $class = "\AuditCongress\Member";
        switch ($route) {
            case "member": $class .= "s"; break;
            case "terms": $class .= "Terms"; break;
            case "socials": $class .= "Socials"; break;
            case "offices": $class .= "Offices"; break;
            case "elections": $class .= "Elections"; break;
        }
        $function = "$class::getByBioguideId";
        return $function($bioguideId);
    }

    public static function HandleMemberDataById($route) {
        $member = API::getQueryArgIfSet("id");
        
        try {
            $data = API::getMemberData($route, $member);
            $result = array($route => $data);
            API::Success($result);
        } catch (Exception $e) {
            API::Error($e->getMessage());
        }
    }


    public static function HandleBioguideToThomasMapping() {
        try {
            $data = Members::getBioguideToThomasIdMapping();
            $mapping = array("mapping" => $data);
            API::Success($mapping);
        } catch (Exception $e) {
            API::Error($e->getMessage());
        }
    }

    public static function HandleValidateSchema() {
        try {
            $schema = Enviroment::getDatabaseSchema();
            $enforcer = new \MySqlConnector\SchemaEnforcer($schema);
            $enforcer->enforceSchema();
            $operations = $enforcer::getDBOperationsList();
            API::Success(array("valid" => true, "operations" => $operations));
        } catch (Exception $e) {
            API::Error($e->getMessage());
        }
    }

    private static function getCongressData() {
        $number = API::getQueryArgIfSet("number");
        $year = API::getQueryArgIfSet("year");
        $current = API::getQueryArgIfSet("current");

        if (isset($number))  return Congresses::getByNumber($number);
        if (isset($year))    return Congresses::getByYear($year);
        if (isset($current)) return Congresses::getCurrent();
                             return Congresses::getAll();
    }

    public static function HandleGetCongress() {
        try {
            $data = API::getCongressData();
            API::Success(array("congress" => $data));
        } catch (Exception $e) {
            API::Error($e->getMessage());
        }
    }

    private static function getSessionData() {
        $congress = API::getQueryArgIfSet("congress");
        $number = API::getQueryArgIfSet("number");
        $chamber = API::getQueryArgIfSet("chamber");
        $date = API::getQueryArgIfSet("date");
        $current = API::getQueryArgIfSet("current");

        if (isset($congress) || isset($number) || isset($chamber)) 
            return Sessions::getByCongressNumberOrChamber($congress, $number, $chamber);
        if (isset($date)) return Sessions::getByDate($date);
        if (isset($current)) return Sessions::getCurrent();
        return Sessions::getAll();
    }

    public static function HandleGetSession() {
        try {
            $data = API::getSessionData();
            API::Success(array("session" => $data));
        } catch (Exception $e) {
            API::Error($e->getMessage());
        }
    }
}

?>