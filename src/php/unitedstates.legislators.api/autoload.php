<?php

define("UNITEDSTATESLEGISLATORS_FOLDER", __DIR__);
require_once AUDITCONGRESS_FOLDER."\\abstract.api.object.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\api\\unitedstates.legislators.api.php";

require_once UNITEDSTATESLEGISLATORS_FOLDER."\\current\\class.members.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\current\\class.committees.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\current\\class.district-offices.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\historical\\class.members.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\historical\\class.committees.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\socials\\class.socials.php";
require_once UNITEDSTATESLEGISLATORS_FOLDER."\\presidents\\class.presidents.php";

?>