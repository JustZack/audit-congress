<?php

require_once "../php/audit.congress/autoload.php";
//$query = new \MySqlConnector\Query("CREATE TABLE `auditcongress`.`example` ( `col1` INT NOT NULL , `col2` INT NOT NULL , `col3` INT NOT NULL , `col4` INT NOT NULL ) ENGINE = InnoDB; ");
//$query = new \MySqlConnector\Query("show databases");
//$query = new \MySqlConnector\Query("show tables");
//var_dump($query->execute()->fetchAll());

$table = new \MySqlConnector\Table("example1");
//var_dump($table->exists());
//var_dump($table->columns());
//var_dump($table->count());
//var_dump($table->create(["id int not null primary key", "name varchar(100)"]));
//var_dump($table->drop());
//var_dump($table->insert(["col1", "col3", "col2", "col4"], [11, 22, 33, 44]));
//var_dump($table->update(["col1", "col2"], [88, 99], "col1 = 11"));
//var_dump($table->delete("col1 = 88"));
//var_dump($table->addColumn("test", "varchar(123) NOT NULL"));
//var_dump($table->modifyColumn("test", "varchar(123) NOT NULL"));
//var_dump($table->dropColumn("test"));

?>