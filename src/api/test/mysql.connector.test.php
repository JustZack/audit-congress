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
    }
}
?>