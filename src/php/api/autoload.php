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
require_once API_FOLDER."\\routes\\terms\class.terms.php";
require_once API_FOLDER."\\routes\\actions\class.actions.php";
require_once API_FOLDER."\\routes\\summaries\class.summaries.php";
require_once API_FOLDER."\\routes\\socials\class.socials.php";
require_once API_FOLDER."\\routes\\offices\class.offices.php";
require_once API_FOLDER."\\routes\\elections\class.elections.php";
require_once API_FOLDER."\\routes\\bills\class.bills.php";
require_once API_FOLDER."\\routes\\cosponsors\class.cosponsors.php";
require_once API_FOLDER."\\routes\\titles\class.titles.php";
require_once API_FOLDER."\\routes\\subjects\class.subjects.php";
require_once API_FOLDER."\\routes\\congress\class.congress.php";
require_once API_FOLDER."\\routes\\session\class.session.php";
require_once API_FOLDER."\\routes\\system\class.validateSchema.php";
require_once API_FOLDER."\\routes\\system\class.bioguideToThomas.php";

?>