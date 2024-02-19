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
            var_dump($table->select(["*"]));

            print("SHOW TABLES:\n");
            var_dump(\MySqlConnector\Table::listTables());
        }

        static function testDatabase($databaseName) {
            $db = new \MySqlConnector\Database($databaseName);
            print("DATABASE $databaseName EXISTS: ".$db->exists()."\n");

            print("DESCRIBE $databaseName:\n");
            var_dump(\MySqlConnector\Database::listDatabases());
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
            var_dump($table->select(["*"], "id = 0"));
        }
    }
}
?>