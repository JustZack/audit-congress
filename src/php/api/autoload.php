<?php

define("API_FOLDER", __DIR__);

require_once API_FOLDER."\base.classes\class.api.exception.php";
require_once API_FOLDER."\base.classes\class.api.exception.thrower.php";
require_once API_FOLDER."\base.classes\class.parameters.php";
require_once API_FOLDER."\base.classes\class.route.group.php";
require_once API_FOLDER."\base.classes\class.route.php";
require_once API_FOLDER."\base.classes\class.pagination.php";

require_once API_FOLDER."\class.runner.php";

require_once API_FOLDER."\\routes\member\class.member.php";
require_once API_FOLDER."\\routes\member\class.member.by.bioguide.id.php";
require_once API_FOLDER."\\routes\member\class.member.by.any.name.php";
require_once API_FOLDER."\\routes\member\class.member.by.filter.php";

require_once API_FOLDER."\\routes\\terms\class.terms.php";
require_once API_FOLDER."\\routes\\terms\class.terms.by.bioguide.id.php";

require_once API_FOLDER."\\routes\\socials\class.socials.php";
require_once API_FOLDER."\\routes\\socials\class.socials.by.bioguide.id.php";

require_once API_FOLDER."\\routes\\offices\class.offices.php";
require_once API_FOLDER."\\routes\\offices\class.offices.by.id.php";

require_once API_FOLDER."\\routes\\elections\class.elections.php";
require_once API_FOLDER."\\routes\\elections\class.elections.by.id.php";

require_once API_FOLDER."\\routes\\bills\class.bills.php";
require_once API_FOLDER."\\routes\\bills\class.bills.by.id.php";
require_once API_FOLDER."\\routes\\bills\class.bills.by.filter.php";

require_once API_FOLDER."\\routes\\cosponsors\class.cosponsors.php";
require_once API_FOLDER."\\routes\\cosponsors\class.cosponsors.by.id.php";
require_once API_FOLDER."\\routes\\cosponsors\class.cosponsors.by.filter.php";

require_once API_FOLDER."\\routes\\titles\class.titles.php";
require_once API_FOLDER."\\routes\\titles\class.titles.by.id.php";
require_once API_FOLDER."\\routes\\titles\class.titles.by.filter.php";

require_once API_FOLDER."\\routes\\subjects\class.subjects.php";
require_once API_FOLDER."\\routes\\subjects\class.subjects.by.id.php";
require_once API_FOLDER."\\routes\\subjects\class.subjects.by.filter.php";

?>