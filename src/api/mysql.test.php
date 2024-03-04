<?php

//Use the testing namespace
namespace APITest;

//Load everything needed for any congress API calls all at once
require_once "test/autoload.php";

//MySqlConnector::testTable("members");
//MySqlConnector::testUpdateRow("example1");
//MySqlConnector::testDeleteRow("example1");
//MySqlConnector::testSelectRow("example1");
//MySqlConnector::testInsertRow("example1");

//MySqlConnector::testSelectRowIteratorArray("example1");
//MySqlConnector::testSelectRowIteratorAssoc("example1");
//MySqlConnector::testSelectRowIteratorRow("example1");
//MySqlConnector::testSelectColumnIterator("example1");

//MySqlConnector::testDatabase("auditcongress");

MySqlConnector::testEnforceSchema();
//MySqlConnector::testSqlMemberById("A5");
MySqlConnector::testSqlMemberByName("bob");
//MySqlConnector::testSqlMemberOfficeById("A000055");
//MySqlConnector::testSqlMemberSocialsById("A000055");
//MySqlConnector::testSqlMemberById("A000055");


?>