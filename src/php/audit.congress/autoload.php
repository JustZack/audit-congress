<?php

define("AUDITCONGRESS_FOLDER", __DIR__);
define("ROOTFOLDER", __DIR__."\\..\\..\\..\\");
define("AUDITCONGRESS_DB_SCHEMA", ROOTFOLDER."audit.congress.schema.json");
define("AUDITCONGRESS_CACHE_SETTINGS", ROOTFOLDER."audit.congress.cache.settings.json");
define("AUDITCONGRESS_CONFIG", ROOTFOLDER."config.json");

require_once AUDITCONGRESS_FOLDER."\..\mysql.connector\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\propublica.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\congress.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\unitedstates.legislators.api\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\cache.tracker\autoload.php";
require_once AUDITCONGRESS_FOLDER."\..\util\autoload.php";

require_once AUDITCONGRESS_FOLDER."\audit.congress.exception.php";
require_once AUDITCONGRESS_FOLDER."\objects\autoload.php";

\Cache\Config::init("CacheStatus", \AuditCongress\Enviroment::getCacheSettings());

?>