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



//require_once "cached.api.php";
//print_r(CachedAPI::GetAPIData("/bill/hr/4040"));

require_once "../congress.api/congress.api.php";

function API_Success($json) {
    header('Content-Type: application/json');
    $json["status"] = "success";
    print_r(json_encode($json));
}

function Get_Index_If_Set($index, $array) {
    if (isset($array[$index])) return $array[$index];
    else return null;
}

$route = $_GET["route"];
switch($route) {
    case "bill": handleBillRoute(); break;
    case "recentBills": handleRecentBills(); break;
}

function handleBillRoute() {
    $congress= Get_Index_If_Set("congress", $_GET);
    $type= Get_Index_If_Set("type", $_GET);
    $number= Get_Index_If_Set("number", $_GET);
    $option= Get_Index_If_Set("option", $_GET);
    
    //Fetching bills by congress
    if (isset($congress)) {
        //Fetching bills by type
        if (isset($type)) {
            //Fetching a specifc bill
            if (isset($number)) {
                //Fetching specific data of a specific bill
                if (isset($option)) {
                } 
                //Fetching a specific bill
                else API_Success(GetBill($congress, $type, $number));
            }
            //Fetching bills by type
            else API_Success(GetBillsByCongressByType($congress, $type));
        }
        //Fetching bills by congress
        else API_Success(GetBillsByCongress($congress));
    } 
    //Fetching all bills is not allowed
    else { }
}

function handleRecentBills() {
    API_Success(GetRecentBills(50));
}

//print_r(json_encode($_GET));

?>