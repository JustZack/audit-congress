<?php

function GetRecentBills($limit, $page) {
    $offset = $limit * ($page - 1);
    $bills = API_CALL("bill", "limit=$limit&offset=$offset&sort=updateDate+desc");
    $bills["page"] = $page;
    return $bills;
}

function GetBills() {
    $bills = API_CALL_BULK("bill", "");
    return $bills;
}
function GetBillsByCongress($congress) {
    $bills = API_CALL_BULK("bill", $congress);
    return $bills;
}
function GetBillsByCongressByType($congress, $type) {
    $bills = API_CALL_BULK("bill", "$congress/$type");
    return $bills;
}
function GetBill($congress, $type, $number) {
    $bill = API_CALL("bill/$congress/$type/$number");
    return $bill;
}
function GetBillActions($congress, $type, $number) {
    $bill_actions = API_CALL_BULK("bill", "$congress/$type/$number/actions");
    return $bill_actions;
}
function GetBillAmendments($congress, $type, $number) {
    $bill_amendments = API_CALL("bill/$congress/$type/$number/amendments");
    return $bill_amendments;
}
function GetBillCommittees($congress, $type, $number) {
    $bill_comitte_reports = API_CALL("bill/$congress/$type/$number/comittees");
    return $bill_comitte_reports;
} 
function GetBillCoSponsors($congress, $type, $number) {
    $bill_cosponsors = API_CALL("bill/$congress/$type/$number/cosponsors");
    return $bill_cosponsors;
}
function GetRelatedBills($congress, $type, $number) {
    $related_bills = API_CALL("bill/$congress/$type/$number/relatedbills");
    return $related_bills;
}
function GetBillSubjects($congress, $type, $number) {
    $related_bills = API_CALL("bill/$congress/$type/$number/subjects");
    return $related_bills;
}
function GetBillSummaries($congress, $type, $number) {
    $bill_summaries = API_CALL("bill/$congress/$type/$number/summaries");
    return $bill_summaries;
}
function GetBillText($congress, $type, $number) {
    $bill_text = API_CALL("bill/$congress/$type/$number/text");
    return $bill_text;
}
function GetBillTitles($congress, $type, $number) {
    $bill_titles = API_CALL("bill/$congress/$type/$number/titles");
    return $bill_titles;
}
?>