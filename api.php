<?php

require_once "src/php/audit.congress/autoload.php";
require_once "src/php/api/autoload.php";

$schema = \AuditCongress\Environment::getAPISchema();
(new \API\Runner($schema))->processRequest();

?>