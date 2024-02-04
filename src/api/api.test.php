<?php

//Load everything needed for any congress API calls all at once
require_once "../php/audit.congress/autoload.php";

function getPPMemberVotesTest($bioid) {
    $mem = new ProPublica\MemberVotes($bioid);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getPPMemberVotesTest("M000087");

function getPPMemberTest($bioid) {
    $mem = new ProPublica\Member($bioid);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getPPMemberTest("M000087");

function getPPBillTest($congress, $slug) {
    $bill = new ProPublica\Bill($congress, $slug);
    $bill->fetchFromApi();
    $bill->printAsJson();
}
//getPPBillTest(118, "hr3377");

function getPPVoteTest($congress, $chamber, $session, $rollCall) {
    $vote = new ProPublica\Vote($congress, $chamber, $session, $rollCall);
    $vote->fetchFromApi();
    $vote->printAsJson();
}
getPPVoteTest(115, "senate", 1, 17);

function getPPCommitteeTest($congress, $chamber, $committeeId) {
    $comm = new ProPublica\Committee($congress, $chamber, $committeeId);
    $comm->fetchFromApi();
    $comm->printAsJson();
}
//getPPCommitteeTest(115, "senate", "SSAF");





function getCApiMemberTest($bioid) {
    $mem = new CongressGov\Member($bioid);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiMemberTest("M000087");

function getCApiBillTest($congress, $type, $number) {
    $mem = new CongressGov\Bill($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiBillTest(118, "hr", "3377");

function getCApiActionsTest($congress, $type, $number, $isBill) {
    $mem = new CongressGov\Actions($congress, $type, $number, $isBill);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiActionsTest(117, "hr", "3076", true);

function getCApiAmendmentsTest($congress, $type, $number, $isBill) {
    $mem = new CongressGov\Amendments($congress, $type, $number, $isBill);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiAmendmentsTest(117, "hr", "3076", true);

function getCApiTextsTest($congress, $type, $number, $isBill) {
    $mem = new CongressGov\Texts($congress, $type, $number, $isBill);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiTextsTest(117, "hr", "3076", true);

function getCApiCosponsorsTest($congress, $type, $number, $isBill) {
    $mem = new CongressGov\Cosponsors($congress, $type, $number, $isBill);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiCosponsorsTest(117, "hr", "3076", true);

function getCApiCommitteesTest($congress, $type, $number) {
    $mem = new CongressGov\Committees($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiCommitteesTest(117, "hr", "3076");

function getCApiRelatedBillsTest($congress, $type, $number) {
    $mem = new CongressGov\RelatedBills($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiRelatedBillsTest(117, "hr", "3076");

function getCApiSubjectsTest($congress, $type, $number) {
    $mem = new CongressGov\Subjects($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiSubjectsTest(117, "hr", "3076");

function getCApiSummariesTest($congress, $type, $number) {
    $mem = new CongressGov\Summaries($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiSummariesTest(117, "hr", "3076");

function getCApiTitlesTest($congress, $type, $number) {
    $mem = new CongressGov\Titles($congress, $type, $number);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getCApiTitlesTest(117, "hr", "3076");


?>
