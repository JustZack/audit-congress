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
/*
    API Entry Point
    * Determine route and call those handlers
*/
$route = $_GET["route"];
switch($route) {
    case "bill": handleBillRoute(); break;
    case "recentBills": handleRecentBillsRoute(); break;
    case "test": API_Success(APICache::CacheRoute("", "")); break;
}

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
function GetBillOption($congress, $type, $number, $option) {
    $data = null;
    switch ($option) {
        case "actions": $data = GetBillActions($congress, $type, $number); break;
        case "amendments": $data = GetBillAmendments($congress, $type, $number); break;
        case "committees": $data = GetBillCommittees($congress, $type, $number); break;
        case "cosponsors": $data = GetBillCosponsors($congress, $type, $number); break;
        case "relatedbills": $data = GetRelatedBills($congress, $type, $number); break;
        case "subjects": $data = GetBillSubjects($congress, $type, $number); break;
        case "summaries": $data = GetBillSummaries($congress, $type, $number); break;
        case "text": $data = GetBillText($congress, $type, $number); break;
        case "titles": $data = GetBillTitles($congress, $type, $number); break;
        default: $data = array();
    }
    return $data;
}

function handleBillRoute() {
    $c = Get_Index_If_Set($_GET, "congress");
    $t = Get_Index_If_Set($_GET, "type");
    $n = Get_Index_If_Set($_GET, "number");
    $o = Get_Index_If_Set($_GET, "option");
    
    $data = 0;
    if (shouldFetchBillOption($c, $t, $n, $o))  
        $data = APICache::UseCache("bill", "GetBillOption", $c, $t, $n, $o);
    else if (shouldFetchBill($c, $t, $n, $o)) 
        $data = APICache::UseCache("bill", "GetBill", $c, $t, $n);
    else if (shouldFetchBillsByCongressByType($c, $t, $n, $o))  
        $data = APICache::UseCache("bill", "GetBillsByCongressByType", $c, $t);
    else if (shouldFetchBillsByCongress($c, $t, $n, $o))        
        $data = APICache::UseCache("bill", "GetBillsByCongress", $c);

    if ($data == 0) API_NotFound();
    else API_Success($data);
}

function handleRecentBillsRoute() {
    $data = APICache::UseCache("recent.bills","GetRecentBills", 50);
    API_Success($data);
}

?>