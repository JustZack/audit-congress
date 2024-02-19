<?php

//Use the testing namespace
namespace APITest;

//Load everything needed for any congress API calls all at once
require_once "test/autoload.php";

//MySqlConnector::testTable("example");
//MySqlConnector::testDatabase("auditcongress");

//$query = new \MySqlConnector\Query("CREATE TABLE `auditcongress`.`example` ( `col1` INT NOT NULL , `col2` INT NOT NULL , `col3` INT NOT NULL , `col4` INT NOT NULL ) ENGINE = InnoDB; ");
//$query = new \MySqlConnector\Query("show databases");
//$query = new \MySqlConnector\Query("show tables");
//var_dump($query->execute()->fetchAll());

//$table = new \MySqlConnector\Table("example1");
//var_dump($table->exists());
//var_dump($table->columns());
//var_dump($table->count("col1 = 22"));
//var_dump($table->count());
//var_dump($table->create(["id int not null primary key", "name varchar(100)"]));
//var_dump($table->drop());
//var_dump($table->insert(["col1", "col3", "col2", "col4"], [11, 22, 33, 44]));
//var_dump($table->update(["col1", "col2"], [88, 99], "col1 = 11"));
//var_dump($table->delete("col1 = 88"));
//var_dump($table->addColumn("test", "varchar(123) NOT NULL"));
//var_dump($table->addColumn("tes3", "varchar(123) NOT NULL"));
//var_dump($table->dropColumn("test"));
//var_dump($table->modifyColumn("tes3", "varchar(123) NOT NULL"));
//var_dump($table->columns()->namesAndTypes());
//var_dump($table->modifyColumn("test", "varchar(123) NOT NULL"));
//var_dump($table->alter("add", "test2", "varchar(123) NOT NULL"));//Should fail
//var_dump($table->dropColumn("test"));
//var_dump($table->select(["*"], "col2 = 2", "col2"));
//var_dump($table->select(["*"]));
//var_dump($table::listTables());

//$db = new \MySqlConnector\Database("auditcongress");
//var_dump($db->exists());
//var_dump($db->create());
//var_dump($db->drop());
//var_dump($db::listDatabases());




?>