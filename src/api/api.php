<?php

require_once "class.api.php";

/*
    API Entry Point
*/
$route = API::getQueryArgIfSet("route");
if ($route !== null) {
    switch($route) {
        case "bill": API::HandleBillRoute(); break;
        case "fullBill": API::HandleFullBillRoute(); break;
        case "recentBills": API::HandleRecentBillsRoute(); break;
        default: API::NotFound($route); break;
    }
} else API::NotFound($route);


?>