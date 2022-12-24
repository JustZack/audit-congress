<?php
require_once "../congress.api/congress.api.php";
require_once "api.cache.php";
require_once "class.api.route.validator.php";
require_once "congress.api.translator.php";

class API {
    private static function Return($data) {
        header('Content-Type: application/json');
        print_r(json_encode($data));
    }
    public static function Success($data) {
        $data["status"] = "Success";
        API::Return($data);
    }
    public static function Error($error) {
        $data = array();
        $data["status"] = "error";
        $data["message"] = $error;
        API::Return($data);
    }
    public static function NotFound($route) {
        $data = array();
        $data["status"] = "not found";
        if (strlen($route) > 0) $data["message"] = "Unknown route: $route";
        else $data["message"] = "No route provided";
        API::Return($data);
    }

    //Get a the given query arg fromm $_GET or null
    public static function getQueryArgIfSet($arg) {
        if (isset($_GET[$arg])) return $_GET[$arg];
        else return null;
    }
    //Determine which translate function to use
    private static function getParseFunction($route) {
        $function = false;
        switch ($route) {
            case "recent.bills": 
                $function = "CongressAPITranslator::translateRecentBills"; 
                break;
            case "bill": 
                //$function = "CongressAPITranslator::translateBill"; 
                break;
            case "bill.actions": 
                //$function = "CongressAPITranslator::translateBillActions"; 
                break;
            case "bill.amendments": 
                //$function = "CongressAPITranslator::translateBillAmendments"; 
                break;
            case "bill.committees": 
                //$function = "CongressAPITranslator::translateBillCommittees"; 
                break;
            case "bill.cosponsors": 
                //$function = "CongressAPITranslator::translateBillCosponsors"; 
                break;
            case "bill.relatedbills": 
                //$function = "CongressAPITranslator::translateBillRelatedBills"; 
                break;
            case "bill.subjects": 
                //$function = "CongressAPITranslator::translateBillSubjects"; 
                break;
            case "bill.summaries": 
                //$function = "CongressAPITranslator::translateBillSummaries"; 
                break;
            case "bill.text": 
                //$function = "CongressAPITranslator::translateTextVersions"; 
                break;
            case "bill.titles": 
                //$function = "CongressAPITranslator::translateTitles"; 
                break;
            default: $function = false;
        }
        return $function;
    }
    //Get the API data, either via fetch or cache
    private static function getAPIData($route, $api_function, $options) {
        $data = APICache::UseCache($route, API::getParseFunction($route), $api_function, ...$options);
        return $data;
    }
    //Do a full API response for the given route and function(...$args)
    private static function doAPIResponse($route, $function, $args) {
        try {
            $data = API::getAPIData($route, $function, $args);
            API::Success($data);
        } catch (Exception $ex) {
            API::Error($ex->getMessage());
        }
    }

    //Handle all individual bill routes
    //Up to one or no api calls will be made if cached
    public static function HandleBillRoute() {
        $congress = API::getQueryArgIfSet("congress");
        $type = API::getQueryArgIfSet("type");
        $number = API::getQueryArgIfSet("number");
        $option = API::getQueryArgIfSet("option");

        $function = -1; $args = [$congress, $type, $number, $option];
        if (APIRouteValidator::shouldFetchBillOption(...$args)) $function = "GetBillOption";
        else if (APIRouteValidator::shouldFetchBill(...$args)) $function = "GetBill";
        else if (APIRouteValidator::shouldFetchBillsByCongressByType(...$args)) $function = "GetBillsByCongressByType";
        else if (APIRouteValidator::shouldFetchBillsByCongress(...$args)) $function = "GetBillsByCongress";

        if ($function == -1) API::NotFound("bill/$congress/$type/$number/$option");
        else API::doAPIResponse("bill", $function, $args);
    }

    //Handle the logic for getting all the API data
    private static function getFullBillData($args) {
        $data = API::getAPIData("bill", "GetBill", $args);
        $bill = $data["bill"];

        $options = GetBillOptionsList();
        
        //Get data for each option
        foreach ($options as $option) {
            $args[3] = $option;
            $optionData = API::getAPIData("bill", "GetBillOption", $args);
            
            //related bills and text have different data keys, this fixes that
            $optionIndex = $option;
            if ($option == "relatedbills") $optionIndex = "relatedBills";
            if ($option == "text") $optionIndex = "textVersions";

            $bill[$optionIndex] = $optionData[$optionIndex];
        }   
        $data["bill"] = $bill;
        $data["request"]["dataType"] = "full";
        return $data;
    }
    //Handle asking for all bill data in one response
    //As many api calls as needed to complete the bill will be made
    //Then compile that information together into a response
    public static function HandleFullBillRoute() {
        $congress = API::getQueryArgIfSet("congress");
        $type = API::getQueryArgIfSet("type");
        $number = API::getQueryArgIfSet("number");
        $args = [$congress, $type, $number, null];

        //Ensure required args are present
        if (APIRouteValidator::shouldFetchBill(...$args)) {
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
    
        if (!APIRouteValidator::shouldFetchRecentBillsPage($page)) $page = 1;
        
        API::doAPIResponse("recent.bills", "GetRecentBills", [25, $page]);
    }
}

?>