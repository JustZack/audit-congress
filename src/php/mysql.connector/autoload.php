<?php


define("MYSQLCONNECTOR_FOLDER", __DIR__);

require_once MYSQLCONNECTOR_FOLDER."\\abstract.sql.object.php";
require_once MYSQLCONNECTOR_FOLDER."\\mysql.connection.php";
require_once MYSQLCONNECTOR_FOLDER."\\mysql.exception.php";

require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.query.builder.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.query.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.result.php";

require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.row.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.schema.enforcer.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.database.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.table.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.columns.php";

require_once ROOTFOLDER."\\env.php";

?>

