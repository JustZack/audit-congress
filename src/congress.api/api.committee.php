<?php

function GetCommittees() {
    $committees = API_CALL("committee");
    return $committees;
}
function GetCommitteesByChamber($chamber) {
    $committees = API_CALL("committee/$chamber");
    return $committees;
}
function GetCommitteesByCongress($congress) {
    $committees = API_CALL("committee/$congress");
    return $committees;
}
function GetCommitteesByCongressByChamber($congress, $chamber) {
    $committees = API_CALL("committee/$congress/$chamber");
    return $committees;
}
function GetCommittee($chamber, $committeeCode) {
    $committees = API_CALL("committee/$chamber/$committeeCode");
    return $committees;
}
function GetCommitteesBills($chamber, $committeeCode) {
    $bills = API_CALL("committee/$chamber/$committeeCode/bills");
    return $bills;
}
function GetCommitteesReports($chamber, $committeeCode) {
    $reports = API_CALL("committee/$chamber/$committeeCode/reports");
    return $report;
}
function GetCommitteesNominations($chamber, $committeeCode) {
    $nominations = API_CALL("committee/$chamber/$committeeCode/nominations");
    return $nominations;
}
function GetCommitteesSenateCommunications($chamber, $committeeCode) {
    $senate_communications = API_CALL("committee/$chamber/$committeeCode/senate-communication");
    return $senate_communications;
}

?>