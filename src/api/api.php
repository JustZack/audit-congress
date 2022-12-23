<?php

    //$json = GetCongresses();
    //print_r($json);
    //$json = GetBillsByCongress("117");
    //print_r($json["bills"][5]);
    //$json = GetBill("117", "HR", 8404);
    //print_r($json["bill"]);
    //$json = GetBillActions("117", "HR", 8404);
    //print_r($json);
    //$json = GetBillTitles("117", "HR", 8404);
    //print_r($json);
    //$json = GetBill("117", "SRES", 869);
    //print_r($json["bill"]);
    ///$json = GetMember("N000002");
    //$json = GetMembers();
    //print_r($json);
    //$json = GetMember("E000259");
    //print_r($json);
    //$json = GetMemberSponsoredLegislation("E000259");
    //print_r($json);
    //$json = GetMemberCoSponsoredLegislation("E000259");
    //print_r($json);
    //$json = GetBills();
    //print_r($json);
    //$json = GetAmendments();
    //print_r(Bill::Get("117", "HR", "521"));
    //$bill = $json["bills"][0];
    //print_r(GetBillCoSponsors($bill["congress"], $bill["type"], $bill["number"]));

//Cache:
//      Items will be rarely be invalidated
//      So, bills, members, actions, titles, etc.
//      These are unchanging
//
//      Listings will be cached on some short timespan
//      So, recent bill listings



require_once "../congress.api/congress.api.php";
require_once "api.cache.php";
require_once "congress.api.translator.php";
/*
    API Entry Point
    * Determine route and call those handlers
*/
$route = Get_Index_If_Set($_GET, "route");
if ($route) {
    switch($route) {
        case "bill": handleBillRoute(); break;
        case "recentBills": handleRecentBillsRoute(); break;
        case "test": API_Success(APICache::CacheRoute("", "")); break;
    }
} else API_NotFound();
function API_Return($data) {
    header('Content-Type: application/json');
    print_r(json_encode($data));
}
function API_NotFound() {
    $data = array();
    $data["status"] = "Not Found";
    API_Return($data);
}
function API_Success($data) {
    $data["status"] = "success";
    API_Return($data);
}

function Get_Index_If_Set($array, $index) {
    if (isset($array[$index])) return $array[$index];
    else return null;
}

function getParseFunction($route) {
    $function = false;
    switch ($route) {
        case "bill": 
            $function = "CongressAPITranslator::translateBill"; 
            break;
        case "recent.bills": 
            $function = "CongressAPITranslator::translateRecentBills"; 
            break;
        default: $function = false;
    }
    return $function;
}
function getAPIData($route, $api_function, ...$options) {
    $data = APICache::UseCache($route, getParseFunction($route), $api_function, ...$options);
    return $data;
}

function shouldFetchBillOption($congress, $type, $number, $option) {
    return isset($congress) && isset($type) && isset($number) && isset($option);
}
function shouldFetchBill($congress, $type, $number, $option) {
    return isset($congress) && isset($type) && isset($number) && !isset($option);
}
function shouldFetchBillsByCongressByType($congress, $type, $number, $option) {
    return isset($congress) && isset($type) && !isset($number) && !isset($option);
}
function shouldFetchBillsByCongress($congress, $type, $number, $option) {
    return isset($congress) && isset($type) && isset($number) && isset($option);
}

function handleBillRoute() {
    $c = Get_Index_If_Set($_GET, "congress");
    $t = Get_Index_If_Set($_GET, "type");
    $n = Get_Index_If_Set($_GET, "number");
    $o = Get_Index_If_Set($_GET, "option");
    
    $data = 0;
    if (shouldFetchBillOption($c, $t, $n, $o))  
        $data = getAPIData("bill", "GetBillOption", $c, $t, $n, $o);
    else if (shouldFetchBill($c, $t, $n, $o)) 
        $data = getAPIData("bill", "GetBill", $c, $t, $n);
    else if (shouldFetchBillsByCongressByType($c, $t, $n, $o))  
        $data = getAPIData("bill", "GetBillsByCongressByType", $c, $t);
    else if (shouldFetchBillsByCongress($c, $t, $n, $o))        
        $data = getAPIData("bill", "GetBillsByCongress", $c);

    if ($data == 0) API_NotFound();
    else API_Success($data);
}

function shouldFetchRecentBillsPage($p) {
    return isset($p);
}

function handleRecentBillsRoute() {
    $p = Get_Index_If_Set($_GET, "page");

    $data = 0;
    if (shouldFetchRecentBillsPage($p)) 
        $data = getAPIData("recent.bills", "GetRecentBills", 25, $p);
    else {
        $data = getAPIData("recent.bills", "GetRecentBills", 25, 1);
    }

    if ($data == 0) API_NotFound();
    else API_Success($data);
}

?>