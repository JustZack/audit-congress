<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        public $schema = null;
        private static $debug_log = true;
        public function __construct($schemaFilePath) {
            $this->schema = json_decode(file_get_contents($schemaFilePath), true);
            $this->enforceSchema();
        }

        private static function debugPrint($message) {
            if (self::$debug_log && strlen($message) > 0) 
                echo $message."\n";
        }

        public function enforceSchema() {
            $schemaTableNames = array();
            //First pass to iterate over tables that should exist in the schema
            foreach ($this->schema["tables"] as $tableSchema) {
                //Fetch the name, columns, and a Columns object for this table in the schema
                list("name"=>$name, "columns"=>$columns) = $tableSchema;
                //Add this table name as a known table
                $schemaTableNames[strtolower($name)] = true;
                //Enforce the known schema onto this table
                self::enforceTableSchema($name, $columns);
            }
            //Second pass to drop all tables not listed in the schema
            self::dropUnknownTables($schemaTableNames);

        }

        //For the given table $name, enforce the given $columns onto its schema
        private static function enforceTableSchema($name, $columns) {
            //Get the schemas columns in the form of a \MySqlConnector\Columns object
            $columnsExpected = self::getSchemaColumnsAsObject($columns);
            //Create an object for this table
            $table = new Table($name);

            //If the table doesnt exist, create the table
            if (!$table->exists()) {
                $columnCreateSqlArr = $columnsExpected->getSqlCreateStrings();
                $table->create($columnCreateSqlArr);
                self::debugPrint("Create Table $name\n");
            }
            //Otherwise enforce the schema for this table
            else self::enforceColumnSchema($table, $columnsExpected, $table->columns());
        }

        //Given the $schemaKnownTables, drop all tables outside of this list
        private static function dropUnknownTables($schemaKnownTables) {
            //Get all tables in the database (according to the current connection)
            $knownTables = Table::showTables();
            //Drop tables not in the given list
            foreach ($knownTables as $name)
                if (!isset($schemaKnownTables[strtolower($name)])) {
                    $table = new Table($name);
                    $table->drop();
                    self::debugPrint("Drop Table $name\n");
                }
        }

        //For the given $table, Check which columns need updated, modified, or dropped
        private static function enforceColumnSchema($table, $columnsExpected, $columnsExisting) {
            $columnsDiff = $columnsExpected->compareEach($columnsExisting);
            foreach ($columnsDiff as $name=>$data) //Broken into handler to simplify
                self::handleEnforceColumnSchema($table, $name, $data);
        }

        private static function dropColumn($table, $name, $type) {
            $table->dropColumn($name, $type);  
            self::debugPrint("Drop $name=>$type"); 
        }

        private static function addColumn($table, $name, $type) {
            $table->addColumn($name, $type);  
            self::debugPrint("Add $name=>$type"); 
        }

        private static function modifyColumn($table, $name, $type) {
            $table->modifyColumn($name, $type);  
            self::debugPrint("Add $name=>$type"); 
        }


        private static function handleEnforceColumnSchema($table, $name, $data) {
            //Break parts of the data into their own vars
            $type = $data["type"];
            $extra = $data["extra"];
            $exists = $data["exists"];
            $matches = $data["matches"];
            
            //Drop extra columns
            if ($extra) self::dropColumn($table, $name, $type);
            //Add missing columns
            else if (!$exists) self::addColumn($table, $name, $type);
            //Modify column mismatches
            else if (!$matches) self::modifyColumn($table, $name, $type);
        }

        //Get the given $schemaColumns as a Columns object, which is then used to enforce schema
        public static function getSchemaColumnsAsObject($schemaColumns) : Columns {
            $columnsInDescribeFormat = array();
            foreach ($schemaColumns as $name=>$data) {
                $primary = isset($data["primary"]) ? "PRI" : "";
                $column = array($name, $data["type"], $data["null"], $primary, "", "");
                array_push($columnsInDescribeFormat, $column);
            }
            return new Columns($columnsInDescribeFormat);
        }
    }
}

?>