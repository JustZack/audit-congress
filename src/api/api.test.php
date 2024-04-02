<?php
//Use the testing namespace
namespace APITest;

//Load everything needed for any congress API calls all at once
require_once "test/autoload.php";

//ProPublica::getMemberVotesTest("M000087");
//ProPublica::getMemberTest("M000087");

//ProPublica::getBillTest(118, "hr3377");
//ProPublica::getVoteTest(115, "senate", 1, 17);

//ProPublica::getCommitteeTest(115, "senate", "SSAF");


//UnitedStatesLegislators::getCurrentMembers();
//UnitedStatesLegislators::getHistoricalMembers();
//UnitedStatesLegislators::getCurrentCommittees();
//UnitedStatesLegislators::getCurrentCommitteeMembership();
//UnitedStatesLegislators::getHistoricalCommittees();;
//UnitedStatesLegislators::getMembersSocials();
//UnitedStatesLegislators::getMembersOffices();
//UnitedStatesLegislators::getPresidents();

//CongressGov::getMemberTest("M000087");
//CongressGov::getMemberListTest();
//CongressGov::getMemberSponsoredLegisTest("M000087");
//CongressGov::getMemberCoSponsoredLegisTest("M000087");

//CongressGov::getBillTest(118, "hr", "3377");

//CongressGov::getBillListTestNoArg();

//CongressGov::getBillListTestCongress(118);
//CongressGov::getBillListTestFactoryOldest(90);
//CongressGov::getBillListTestFactoryNewest(90);

//CongressGov::getBillListTestCongressType(90, "hr");
//CongressGov::getBillListTestCongressType(90, "s");
//CongressGov::getBillListTestCongressType(118, "hr");

//CongressGov::getActionsTest(117, "hr", "3076", true);
//CongressGov::getAmendmentsTest(117, "hr", "3076", true);
//CongressGov::getTextsTest(117, "hr", "3076", true);
//CongressGov::getCosponsorsTest(117, "hr", "3076", true);
//CongressGov::getCommitteesTest(117, "hr", "3076");
//CongressGov::getRelatedBillsTest(117, "hr", "3076");
//CongressGov::getSubjectsTest(117, "hr", "3076");
//CongressGov::getSummariesTest(117, "hr", "3076");
//CongressGov::getTitlesTest(117, "hr", "3076");
//CongressGov::getCongressesTest();

CacheTracker::testGetCacheNextUpdate("bulk-bill");
?>