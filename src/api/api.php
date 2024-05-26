<?php

//Load everything needed for any congress API calls all at once
require_once "../php/audit.congress/autoload.php";
require_once "class.api.php";
require_once "../php/api/autoload.php";
//require_once "old/class.api.old.php";

/*
    API Entry Point
*/

\API\Runner::processRequest();

?>