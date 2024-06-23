<?php


define("MYSQLCONNECTOR_FOLDER", __DIR__);

require_once MYSQLCONNECTOR_FOLDER."\\mysql.connection.php";
require_once MYSQLCONNECTOR_FOLDER."\\mysql.exception.php";
require_once MYSQLCONNECTOR_FOLDER."\\mysql.exception.thrower.php";

require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.query.options.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.query.builder.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.query.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.result.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.operators.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.parameterized.interfaces.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.condition.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.condition.group.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.insert.group.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.join.clause.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\mysql.where.clause.php";
require_once MYSQLCONNECTOR_FOLDER."\\querys\query.wrapper.php";

require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.row.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.schema.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.table.schema.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.schema.enforcer.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.database.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.table.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.compareable.set.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.columns.php";
require_once MYSQLCONNECTOR_FOLDER."\\structures\mysql.indexes.php";



?>

