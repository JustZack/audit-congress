<?php
require_once "../congress.api/congress.api.php";
require_once "api.cache.php";
require_once "class.api.route.validator.php";
require_once "congress.api.translator.php";

class API {
    /*
        Generic API functions to complete the response
    */
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

    /*
        Helper functions
    */
    //Get a the given query arg fromm $_GET or null
    public static function getQueryArgIfSet($arg) {
        if (isset($_GET[$arg])) return $_GET[$arg];
        else return null;
    }
    //Get the API data, either via fetch or cache
    private static function getAPIData($route, $api_function, $options) {
        $translationFunction = CongressAPITranslator::determineTranslateFunction($route);
        $data = APICache::UseCache($route, $translationFunction, $api_function, ...$options);
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

    /*
        BILL ROUTES
    */
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
        $start = time();
        
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

        $end = time();
        $data["bill"] = $bill;
        $data["request"]["dataType"] = "full";
        $data["request"]["time"] = ($end-$start);
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
    
        if (!APIRouteValidator::shouldFetchRecentBillsPage($page)) $page = 1;
        
        API::doAPIResponse("recent.bills", "GetRecentBills", [25, $page]);
    }



    /*
        MEMBER ROUTES
    */
    //Handle the memeber route and options
    public static function HandleMemberRoute() {
        $member = API::getQueryArgIfSet("member");
        $option = API::getQueryArgIfSet("option");

        $function = -1; $args = [$member, $option];
        if (APIRouteValidator::shouldFetchMembersList($member)) $function = "GetMembers";
        if (APIRouteValidator::shouldFetchMember($member)) $function = "GetMember";
        if (APIRouteValidator::shouldFetchMemberOption($member, $option)) $function = "GetMemberOption";

        if ($function == -1) API::NotFound("member/$member/$option");
        else API::doAPIResponse("member", $function, $args);
    }
    //Handle the logic for getting all the member api data
    private static function getFullMemberData($args) {
        $start = time();

        $data = API::getAPIData("member", "GetMember", $args);
        $member = $data["member"];

        $options = GetMemberOptionsList();
        
        //Get data for each option
        foreach ($options as $option) {
            $args[1] = $option;
            $optionData = API::getAPIData("member", "GetMemberOption", $args);
            
            //both options have different data keys, this fixes that
            $optionIndex = $option;
            if ($option == "sponsored-legislation") $optionIndex = "sponsoredLegislation";
            if ($option == "cosponsored-legislation") $optionIndex = "cosponsoredLegislation";
            $member[$optionIndex] = $optionData[$optionIndex];
        }   

        $end = time();
        $data["member"] = $member;
        $data["request"]["dataType"] = "full";
        $data["request"]["time"] = ($end-$start);
        return $data;
    }
    //Handle asking for all member data in one response
    public static function HandleFullMemberRoute() {
        $member = API::getQueryArgIfSet("member");

        $args = [$member, null];
        if (APIRouteValidator::shouldFetchFullMember(...$args)) {
            try {
                $data = API::getFullMemberData($args);
                API::Success($data);
            } catch (Exception $e) {
                API::Error($e->getMessage());
            }
        } else {
            API::NotFound("fullMember/$member");
        }
    }
}

?>