<?php

namespace APITest {
    class MySqlConnector {
        static function testTable($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            print("TABLE $tableName EXISTS: ".$table->exists()."\n");

            print("DESCRIBE $tableName:\n");
            var_dump($table->columns()->namesAndTypes());

            print("SELECT COUNT(*) FROM $tableName: ".$table->count()."\n");

            print("SELECT * FROM $tableName:\n");
            var_dump($table->select(["*"])->fetchAll());
        }
        
        static function testDatabase($databaseName) {
            $db = new \MySqlConnector\Database($databaseName);
            print("DATABASE $databaseName EXISTS: ".$db->exists()."\n");
            
            print("DESCRIBE $databaseName:\n");
            var_dump(\MySqlConnector\Database::showDatabases());

            print("SHOW TABLES IN $databaseName:\n");
            var_dump($db->showTables());
        }

        static function testInsertRow($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            var_dump($table->insert(["id", "name", "test", "tes3"], [((int)rand()), "namehere", "test", "x3"]));
        }

        static function testUpdateRow($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            var_dump($table->update(["id", "name", "test", "tes3"], [11, "dabba","updateddhfjajkld","here"], "id = 11"));
        }

        static function testDeleteRow($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            var_dump($table->delete("id = 148174992"));
        }

        static function testSelectRow($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            var_dump($table->select(["*"], "id = 0")->fetchAll());
        }

        static function testSelectRowIteratorArray($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            $result = $table->select(["*"]);
            while ($row = $result->fetchArray()) var_dump($row);
        }

        static function testSelectRowIteratorAssoc($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            $result = $table->select(["*"]);
            while ($row = $result->fetchAssoc()) var_dump($row);
        }

        static function testSelectRowIteratorRow($tableName) {
            $table = new \MySqlConnector\Table($tableName);
            $result = $table->select(["*"]);
            while ($row = $result->fetchRow()) var_dump($row);
        }

        static function testSelectColumnIterator($tableName) {
            echo "This should fail with php < 8.0";
            $table = new \MySqlConnector\Table($tableName);
            $result = $table->select(["*"]);
            while ($row = $result->fetchColumn(1)) var_dump($row);
        }

        static function testSqlMemberByName($fname = null, $lname = null, $isCurrent = null) {
            $rows = \AuditCongress\Members::getByName($fname, $lname, $isCurrent);
            var_dump($rows);
        }

        static function testSqlMemberOfficeById($id) {
            $rows = \AuditCongress\MemberOffices::getByBioguideId($id);
            var_dump($rows);
        }

        static function testSqlMemberSocialsById($id) {
            $rows = \AuditCongress\MemberSocials::getByBioguideId($id);
            var_dump($rows);
        }

        static function testSqlMemberById($id, $isCurrent = null) {
            $rows = \AuditCongress\Members::getByBioguideId($id, $isCurrent);
            var_dump($rows);
        }

        static function testSqTermsByPartyByYear($party, $year = null) {
            $rows = \AuditCongress\MemberTerms::getByParty($party, $year);
            var_dump($rows);
        }

        static function testSqTermsByStateByYear($state, $year = null) {
            $rows = \AuditCongress\MemberTerms::getByState($state, $year);
            var_dump($rows);
        }

        static function testGetSenators($state = null, $isCurrent = null) {
            $rows = \AuditCongress\Members::getSenators($state, $isCurrent);
            var_dump($rows);
        }

        static function testGetReps($state = null, $isCurrent = null) {
            $rows = \AuditCongress\Members::getRepresentatives($state, $isCurrent);
            var_dump($rows);
        }

        static function testGetByState($state = null, $isCurrent = null) {
            $rows = \AuditCongress\Members::getByState($state, $isCurrent);
            var_dump($rows);
        }
        
        static function testEnforceSchema() {
            $schema = new \MySqlConnector\SchemaEnforcer(AUDITCONGRESS_DB_SCHEMA);
        }
    }
}
?>