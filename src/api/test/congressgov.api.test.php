<?php

namespace APITest {
    class CongressGov {
        static function getMemberTest($bioid) {
            $mem = new \CongressGov\Member($bioid);
            $mem->printAsJson();
        }

        static function getMemberSponsoredLegisTest($bioid) {
            $mem = new \CongressGov\SponsoredLegislation($bioid);
            $mem->printAsJson();
        }

        static function getMemberCoSponsoredLegisTest($bioid) {
            $mem = new \CongressGov\CoSponsoredLegislation($bioid);
            $mem->printAsJson();
        }

        static function getMemberListTest() {
            $mem = new \CongressGov\MemberList();
            $mem->printAsJson();
        }

        static function getBillTest($congress, $type, $number) {
            $mem = new \CongressGov\Bill($congress, $type, $number);
            $mem->printAsJson();
        }

        static function getBillListTestNoArg() {
            $mem = new \CongressGov\BillList();
            $mem->printAsJson();
        }

        static function getBillListTestFactoryOldest($congress) {
            $mem = \CongressGov\BillList::getByOldestFirst($congress);
            $mem->printAsJson();
        }

        static function getBillListTestFactoryNewest($congress) {
            $mem = \CongressGov\BillList::getByNewestFirst($congress);
            $mem->printAsJson();
        }

        static function getBillListTestCongress($congress) {
            $mem = new \CongressGov\BillList($congress);
            $mem->printAsJson();
        }


        static function getBillListTestCongressType($congress, $type) {
            $mem = new \CongressGov\BillList($congress, $type);
            $mem->printAsJson();
        }


        static function getActionsTest($congress, $type, $number, $isBill) {
            $mem = new \CongressGov\Actions($congress, $type, $number, $isBill);
            $mem->printAsJson();
        }


        static function getAmendmentsTest($congress, $type, $number, $isBill) {
            $mem = new \CongressGov\Amendments($congress, $type, $number, $isBill);
            $mem->printAsJson();
        }
        

        static function getTextsTest($congress, $type, $number, $isBill) {
            $mem = new \CongressGov\Texts($congress, $type, $number, $isBill);
            $mem->printAsJson();
        }
        

        static function getCosponsorsTest($congress, $type, $number, $isBill) {
            $mem = new \CongressGov\Cosponsors($congress, $type, $number, $isBill);
            $mem->printAsJson();
        }


        static function getCommitteesTest($congress, $type, $number) {
            $mem = new \CongressGov\Committees($congress, $type, $number);
            $mem->printAsJson();
        }
        

        static function getRelatedBillsTest($congress, $type, $number) {
            $mem = new \CongressGov\RelatedBills($congress, $type, $number);
            $mem->printAsJson();
        }
        

        static function getSubjectsTest($congress, $type, $number) {
            $mem = new \CongressGov\Subjects($congress, $type, $number);
            $mem->printAsJson();
        }
        

        static function getSummariesTest($congress, $type, $number) {
            $mem = new \CongressGov\Summaries($congress, $type, $number);
            $mem->printAsJson();
        }
        

        static function getTitlesTest($congress, $type, $number) {
            $mem = new \CongressGov\Titles($congress, $type, $number);
            $mem->printAsJson();
        }
    }
}
?>
