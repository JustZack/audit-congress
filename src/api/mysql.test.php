<?php

require_once "../php/audit.congress/autoload.php";
//$query = new \MySqlConnector\Query("CREATE TABLE `auditcongress`.`example` ( `col1` INT NOT NULL , `col2` INT NOT NULL , `col3` INT NOT NULL , `col4` INT NOT NULL ) ENGINE = InnoDB; ");
//$query = new \MySqlConnector\Query("show databases");
$query = new \MySqlConnector\Query("show tables");
var_dump($query->execute()->fetchAll());

?>