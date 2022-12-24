<?php

function GetRecentBills($limit, $page) {
    $offset = $limit * ($page - 1);
    $bills = CONGRESS_API_CALL("bill", "limit=$limit&offset=$offset&sort=updateDate+desc");
    $bills["page"] = $page;
    $bills["offset"] = $offset;
    return $bills;
}

function GetBills() {
    $bills = CONGRESS_API_CALL_BULK("bill", "");
    return $bills;
}
function GetBillsByCongress($congress) {
    $bills = CONGRESS_API_CALL_BULK("bill", $congress);
    return $bills;
}
function GetBillsByCongressByType($congress, $type) {
    $bills = CONGRESS_API_CALL_BULK("bill", "$congress/$type");
    return $bills;
}

function GetBillOptionsList() {
    return ["actions", "amendments", "committees", "cosponsors", "relatedbills", "subjects", "summaries", "text", "titles"];
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

function GetBill($congress, $type, $number) {
    $bill = CONGRESS_API_CALL("bill/$congress/$type/$number");
    return $bill;
}
function GetBillActions($congress, $type, $number) {
    $bill_actions = CONGRESS_API_CALL_BULK("bill", "$congress/$type/$number/actions");
    return $bill_actions;
}
function GetBillAmendments($congress, $type, $number) {
    $bill_amendments = CONGRESS_API_CALL("bill/$congress/$type/$number/amendments");
    return $bill_amendments;
}
function GetBillCommittees($congress, $type, $number) {
    $bill_comitte_reports = CONGRESS_API_CALL("bill/$congress/$type/$number/committees");
    return $bill_comitte_reports;
} 
function GetBillCoSponsors($congress, $type, $number) {
    $bill_cosponsors = CONGRESS_API_CALL("bill/$congress/$type/$number/cosponsors");
    return $bill_cosponsors;
}
function GetRelatedBills($congress, $type, $number) {
    $related_bills = CONGRESS_API_CALL("bill/$congress/$type/$number/relatedbills");
    return $related_bills;
}
function GetBillSubjects($congress, $type, $number) {
    $related_bills = CONGRESS_API_CALL("bill/$congress/$type/$number/subjects");
    return $related_bills;
}
function GetBillSummaries($congress, $type, $number) {
    $bill_summaries = CONGRESS_API_CALL("bill/$congress/$type/$number/summaries");
    return $bill_summaries;
}
function GetBillText($congress, $type, $number) {
    $bill_text = CONGRESS_API_CALL("bill/$congress/$type/$number/text");
    return $bill_text;
}
function GetBillTitles($congress, $type, $number) {
    $bill_titles = CONGRESS_API_CALL("bill/$congress/$type/$number/titles");
    return $bill_titles;
}
?>