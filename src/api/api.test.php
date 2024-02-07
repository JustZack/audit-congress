<?php

//Load everything needed for any congress API calls all at once
require_once "../php/audit.congress/autoload.php";

//ProPublicaApiTest::getPPMemberVotesTest("M000087");
//ProPublicaApiTest::getPPMemberTest("M000087");

//ProPublicaApiTest::getPPBillTest(118, "hr3377");
//ProPublicaApiTest::getPPVoteTest(115, "senate", 1, 17);

//ProPublicaApiTest::getPPCommitteeTest(115, "senate", "SSAF");




//CongressGovApiTest::getCApiMemberTest("M000087");

//CongressGovApiTest::getCApiBillTest(118, "hr", "3377");

//CongressGovApiTest::getCApiBillListTestNoArg();

//CongressGovApiTest::getCApiBillListTestCongress(118);
//CongressGovApiTest::getCApiBillListTestFactoryOldest(90);
//CongressGovApiTest::getCApiBillListTestFactoryNewest(90);

//CongressGovApiTest::getCApiBillListTestCongressType(90, "hr");
//CongressGovApiTest::getCApiBillListTestCongressType(90, "s");
//CongressGovApiTest::getCApiBillListTestCongressType(118, "hr");

//CongressGovApiTest::getCApiActionsTest(117, "hr", "3076", true);
//CongressGovApiTest::getCApiAmendmentsTest(117, "hr", "3076", true);
//CongressGovApiTest::getCApiTextsTest(117, "hr", "3076", true);
//CongressGovApiTest::getCApiCosponsorsTest(117, "hr", "3076", true);
//CongressGovApiTest::getCApiCommitteesTest(117, "hr", "3076");
//CongressGovApiTest::getCApiRelatedBillsTest(117, "hr", "3076");
//CongressGovApiTest::getCApiSubjectsTest(117, "hr", "3076");
//CongressGovApiTest::getCApiSummariesTest(117, "hr", "3076");
//CongressGovApiTest::getCApiTitlesTest(117, "hr", "3076");




class ProPublicaApiTest {
    static function getPPMemberVotesTest($bioid) {
        $mem = new ProPublica\MemberVotes($bioid);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getPPMemberTest($bioid) {
        $mem = new ProPublica\Member($bioid);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getPPBillTest($congress, $slug) {
        $bill = new ProPublica\Bill($congress, $slug);
        $bill->fetchFromApi();
        $bill->printAsJson();
    }


    static function getPPVoteTest($congress, $chamber, $session, $rollCall) {
        $vote = new ProPublica\Vote($congress, $chamber, $session, $rollCall);
        $vote->fetchFromApi();
        $vote->printAsJson();
    }
    

    static function getPPCommitteeTest($congress, $chamber, $committeeId) {
        $comm = new ProPublica\Committee($congress, $chamber, $committeeId);
        $comm->fetchFromApi();
        $comm->printAsJson();
    }
}


class CongressGovApiTest {
    static function getCApiMemberTest($bioid) {
        $mem = new CongressGov\Member($bioid);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getCApiBillTest($congress, $type, $number) {
        $mem = new CongressGov\Bill($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }

    static function getCApiBillListTestNoArg() {
        $mem = new CongressGov\BillList();
        $mem->fetchFromApi();
        $mem->printAsJson();
    }

    static function getCApiBillListTestFactoryOldest($congress) {
        $mem = CongressGov\BillList::getByOldestFirst($congress);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }

    static function getCApiBillListTestFactoryNewest($congress) {
        $mem = CongressGov\BillList::getByNewestFirst($congress);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }

    static function getCApiBillListTestCongress($congress) {
        $mem = new CongressGov\BillList($congress);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getCApiBillListTestCongressType($congress, $type) {
        $mem = new CongressGov\BillList($congress, $type);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getCApiActionsTest($congress, $type, $number, $isBill) {
        $mem = new CongressGov\Actions($congress, $type, $number, $isBill);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getCApiAmendmentsTest($congress, $type, $number, $isBill) {
        $mem = new CongressGov\Amendments($congress, $type, $number, $isBill);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiTextsTest($congress, $type, $number, $isBill) {
        $mem = new CongressGov\Texts($congress, $type, $number, $isBill);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiCosponsorsTest($congress, $type, $number, $isBill) {
        $mem = new CongressGov\Cosponsors($congress, $type, $number, $isBill);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }


    static function getCApiCommitteesTest($congress, $type, $number) {
        $mem = new CongressGov\Committees($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiRelatedBillsTest($congress, $type, $number) {
        $mem = new CongressGov\RelatedBills($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiSubjectsTest($congress, $type, $number) {
        $mem = new CongressGov\Subjects($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiSummariesTest($congress, $type, $number) {
        $mem = new CongressGov\Summaries($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
    

    static function getCApiTitlesTest($congress, $type, $number) {
        $mem = new CongressGov\Titles($congress, $type, $number);
        $mem->fetchFromApi();
        $mem->printAsJson();
    }
}
?>
