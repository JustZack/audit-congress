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

        static function testSqlMemberByName($fname, $middle, $lname) {
            $result = \AuditCongress\Members::getByName($fname, $middle, $lname);
            var_dump($result->fetchAllAssoc());
            //var_dump($result);
        }

        static function testSqlMemberOfficeById($id) {
            $result = \AuditCongress\MemberOffices::getByBioguideId($id);
            var_dump($result->fetchAllAssoc());
        }

        static function testSqlMemberSocialsById($id) {
            $result = \AuditCongress\MemberSocials::getByBioguideId($id);
            var_dump($result->fetchAllAssoc());
        }

        static function testSqlMemberById($id) {
            $result = \AuditCongress\Members::getByBioguideId($id);
            var_dump($result->fetchAllAssoc());
        }

        static function testEnforceSchema() {
            $schema = new \MySqlConnector\SchemaEnforcer(AUDITCONGRESS_DB_SCHEMA);
        }
    }
}
?>