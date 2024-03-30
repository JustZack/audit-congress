<?php

define("AUDITCONGRESS_FOLDER", __DIR__);
define("ROOTFOLDER", __DIR__."\\..\\..\\..\\");
define("AUDITCONGRESS_DB_SCHEMA", AUDITCONGRESS_FOLDER."\schema\audit.congress.schema.json");

require_once AUDITCONGRESS_FOLDER."\..\mysql.connector\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\propublica.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\congress.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\unitedstates.legislators.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\cache.tracker\autoload.php";

require_once AUDITCONGRESS_FOLDER."\audit.congress.exception.php";
require_once AUDITCONGRESS_FOLDER."\objects\autoload.php";

\AuditCongress\CacheTracker::initCacheTracker("CacheStatus");

?>