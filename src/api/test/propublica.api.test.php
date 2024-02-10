<?php

namespace APITest {
    class ProPublica {
        static function getMemberVotesTest($bioid) {
            $mem = new \ProPublica\MemberVotes($bioid);
            $mem->fetchFromApi();
            $mem->printAsJson();
        }


        static function getMemberTest($bioid) {
            $mem = new \ProPublica\Member($bioid);
            $mem->fetchFromApi();
            $mem->printAsJson();
        }


        static function getBillTest($congress, $slug) {
            $bill = new \ProPublica\Bill($congress, $slug);
            $bill->fetchFromApi();
            $bill->printAsJson();
        }


        static function getVoteTest($congress, $chamber, $session, $rollCall) {
            $vote = new \ProPublica\Vote($congress, $chamber, $session, $rollCall);
            $vote->fetchFromApi();
            $vote->printAsJson();
        }
        

        static function getCommitteeTest($congress, $chamber, $committeeId) {
            $comm = new \ProPublica\Committee($congress, $chamber, $committeeId);
            $comm->fetchFromApi();
            $comm->printAsJson();
        }
    }
}
?>