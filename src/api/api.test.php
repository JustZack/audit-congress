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
//getPPVoteTest(115, "senate", 1, 17);

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

function getCApiBillTest($bioid) {
    $mem = new CongressGov\Bill(118, "hr", "3377");
    $mem->fetchFromApi();
    $mem->printAsJson();
}
getCApiBillTest("M000087");

?>
