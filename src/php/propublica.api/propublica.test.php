<?php
require_once "member/class.member.php";
require_once "member/class.member.votes.php";
require_once "bill/class.bill.php";
require_once "vote/class.vote.php";

function getPPMemberVotesTest($bioid) {
    $mem = new ProPublica\MemberVotes($bioid);
    $mem->fetchFromApi();
    $mem->printAsJson();
}
//getPPMemberVotesTest("K000388");

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
getPPBillTest(118, "hr3377");

function getPPVoteTest($congress, $chamber, $session, $rollCall) {
    $vote = new ProPublica\Vote($congress, $chamber, $session, $rollCall);
    $vote->fetchFromApi();
    $vote->printAsJson();
}
//getPPVoteTest(115, "senate", 1, 17);

?>
