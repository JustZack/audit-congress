<?php

namespace APITest {
    class UnitedStatesLegislators {
        static function getCurrentMembers() {
            $mem = new \UnitedStatesLegislators\CurrentMembers();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getHistoricalMembers() {
            $mem = new \UnitedStatesLegislators\HistoricalMembers();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getCurrentCommittees() {
            $mem = new \UnitedStatesLegislators\CurrentCommittees();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getHistoricalCommittees() {
            $mem = new \UnitedStatesLegislators\HistoricalCommittees();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getMembersOffices() {
            $mem = new \UnitedStatesLegislators\CurrentDistrictOffices();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getMembersSocials() {
            $mem = new \UnitedStatesLegislators\Socials();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }

        static function getPresidents() {
            $mem = new \UnitedStatesLegislators\Socials();
            $mem->fetchFromApi();
            $mem->printAsJson();
        }
    }
}
?>