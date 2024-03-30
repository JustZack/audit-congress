<?php


define("CONGRESSGOV_FOLDER", __DIR__);
require_once AUDITCONGRESS_FOLDER."\\abstract.api.object.php";
require_once CONGRESSGOV_FOLDER."\\api\\congress.api.php";

require_once CONGRESSGOV_FOLDER."\\member\\class.member.php";
require_once CONGRESSGOV_FOLDER."\\member\\class.cosponsored-legislation.php";
require_once CONGRESSGOV_FOLDER."\\member\\class.sponsored-legislation.php";
require_once CONGRESSGOV_FOLDER."\\member\\class.legislation.php";
require_once CONGRESSGOV_FOLDER."\\member\\class.memberlist.php";
require_once CONGRESSGOV_FOLDER."\\bill\\class.bill.php";
require_once CONGRESSGOV_FOLDER."\\bill\\class.billlist.php";
require_once CONGRESSGOV_FOLDER."\\congress\\class.congresses.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.actions.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.amendments.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.cosponsors.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.texts.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.committees.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.related-bills.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.subjects.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.summaries.php";
require_once CONGRESSGOV_FOLDER."\\subroutes\\class.titles.php";

?>

