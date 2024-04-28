<?php

define("API_FOLDER", __DIR__);

require_once API_FOLDER."\class.runner.php";

require_once API_FOLDER."\base.classes\class.api.exception.php";
require_once API_FOLDER."\base.classes\class.api.exception.thrower.php";
require_once API_FOLDER."\base.classes\class.parameters.php";
require_once API_FOLDER."\base.classes\class.route.group.php";
require_once API_FOLDER."\base.classes\class.route.php";

require_once API_FOLDER."\\routes\member\class.member.php";
require_once API_FOLDER."\\routes\member\class.member.by.bioguide.id.php";
require_once API_FOLDER."\\routes\member\class.member.by.any.name.php";
require_once API_FOLDER."\\routes\member\class.member.by.state.php";
require_once API_FOLDER."\\routes\member\class.member.by.type.by.state.php";

?>