<?php

namespace APITest {
    class UnitedStatesLegislators {
        static function getCurrentMembers() {
            $mem = new \UnitedStatesLegislators\CurrentMembers();
            $mem->printAsJson();
        }

        static function getHistoricalMembers() {
            $mem = new \UnitedStatesLegislators\HistoricalMembers();
            $mem->printAsJson();
        }

        static function getCurrentCommittees() {
            $mem = new \UnitedStatesLegislators\CurrentCommittees();
            $mem->printAsJson();
        }

        static function getCurrentCommitteeMembership() {
            $mem = new \UnitedStatesLegislators\CurrentCommitteeMembership();
            $mem->printAsJson();
        }

        static function getHistoricalCommittees() {
            $mem = new \UnitedStatesLegislators\HistoricalCommittees();
            $mem->printAsJson();
        }

        static function getMembersOffices() {
            $mem = new \UnitedStatesLegislators\CurrentDistrictOffices();
            $mem->printAsJson();
        }

        static function getMembersSocials() {
            $mem = new \UnitedStatesLegislators\Socials();
            $mem->printAsJson();
        }

        static function getPresidents() {
            $mem = new \UnitedStatesLegislators\Presidents();
            $mem->printAsJson();
        }
    }
}
?>