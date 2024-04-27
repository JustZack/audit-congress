<?php

define("API_FOLDER", __DIR__);

require_once API_FOLDER."\base.classes\class.api.exception.php";
require_once API_FOLDER."\base.classes\class.parameters.php";
require_once API_FOLDER."\base.classes\class.route.group.php";
require_once API_FOLDER."\base.classes\class.route.php";

require_once API_FOLDER."\\routes\member\class.member.php";
require_once API_FOLDER."\\routes\member\class.member.by.id.php";

?>