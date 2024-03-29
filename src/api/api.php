<?php

//Load everything needed for any congress API calls all at once
require_once "../php/audit.congress/autoload.php";
require_once "class.api.php";
//require_once "old/class.api.old.php";

/*
    API Entry Point
*/
$route = API::getQueryArgIfSet("route");
if ($route !== null) {
    switch($route) {
        case "bill": API::HandleBillRoute(); break;
        case "fullBill": API::HandleFullBillRoute(); break;
        
        case "member": API::HandleMemberRoute(); break;
        case "fullMember": API::HandleFullMemberRoute(); break;
        
        case "congress": API::HandleGetCongress(); break;
        case "session": API::HandleGetSession(); break;

        case "recentBills": API::HandleRecentBillsRoute(); break;
        case "bioguideToThomas": API::HandleBioguideToThomasMapping(); break;
        case "validateSchema": API::HandleValidateSchema(); break;
        default: API::NotFound($route); break;
    }
} else API::NotFound($route);


?>