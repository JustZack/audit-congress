<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        public $schema = null;
        private static $debug_log = false;
        private static $operations = array();

        public function __construct($schema) {
            $this->schema = $schema;

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
                //Create an object for this table
                $table = new Table($name);
                //Enforce the known schema onto this table
                self::enforceTableSchema($name, $columns);

                $indexes = array_key_exists("indexes", $tableSchema) ? $tableSchema["indexes"] : array();
                self::enforceTableIndexes($table, $indexes);
            }
            //Second pass to drop all tables not listed in the schema
            self::dropUnknownTables($schemaTableNames);
        }

        private static function addDBOperation($operation) { 
            self::debugPrint($operation);
            array_push(self::$operations, $operation); 
        }
        public static function getDBOperationsList() { return self::$operations; }

        //For the given table $name, enforce the given $columns onto its schema
        private static function enforceTableSchema($name, $columns) {
            //Get the schemas columns in the form of a \MySqlConnector\Columns object
            $columnsExpected = self::getSchemaColumnsAsObject($columns);
            $table = new Table($name);

            //If the table doesnt exist, create the table
            if (!$table->exists()) {
                $columnCreateSqlArr = $columnsExpected->getSqlCreateStrings();
                $table->create($columnCreateSqlArr);
                self::addDBOperation("Create Table $name");
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
                    self::addDBOperation("Drop Table $name");
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
            self::addDBOperation("Drop $name=>$type"); 
        }

        private static function addColumn($table, $name, $type) {
            $table->addColumn($name, $type);  
            self::addDBOperation("Add $name=>$type"); 
        }

        private static function modifyColumn($table, $name, $type) {
            $table->modifyColumn($name, $type);  
            self::addDBOperation("Add $name=>$type"); 
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
                $extra = isset($data["extra"]) ? $data["extra"] : "";
                $column = array($name, $data["type"], $data["null"], $primary, "", $extra);
                array_push($columnsInDescribeFormat, $column);
            }
            return new Columns($columnsInDescribeFormat);
        }

        public static function getSchemaIndexesAsObject($schemaIndexes) : Indexes {
            $IndexesInDescribeFormat = array();
            foreach ($schemaIndexes as $name=>$indexes) {
                for ($i = 0;$i < count($indexes);$i++) {
                    $index = array(1 => 1, 2 => $name, 4 => $indexes[$i], 
                                   5 => "A", 6 => 0, 10 => "BTREE", 13 => "YES");
                    array_push($IndexesInDescribeFormat, $index);
                }
            }
            return new Indexes($IndexesInDescribeFormat);
        }

        
        private static function enforceTableIndexes($table, $schemaIndexes) {
            //Fetch existing indexes
            $indexesExisting = $table->indexes();
            //Fetch expected indexes
            $indexesExpected = self::getSchemaIndexesAsObject($schemaIndexes);
            //Compute the difference between them
            $indexesDiff = $indexesExpected->compare($indexesExisting);
            //Decide what to do with each difference
            foreach ($indexesDiff as $name=>$indexDiff) //Broken into handler to simplify
                self::handleEnforceIndexSchema($table, $indexDiff);
        }

        private static function handleEnforceIndexSchema($table, $indexDiff) {
            $index = $indexDiff->item();
            $dbOperation = null;

            //Drop extra index
            if ($indexDiff->extra()) {
                $dbOperation = sprintf("Drop Index %s=>%s from table %s", $index->name(), $index->columns(), $table->name); 
                $table->alterIndex(AlterType::DROP, $index);
            }
            //Add missing index
            else if (!$indexDiff->exists()) {
                $dbOperation = sprintf("Add Index %s=>%s to table %s", $index->name(), $index->columns(), $table->name); 
                $table->alterIndex(AlterType::ADD, $index);
            }
            //Modify index mismatch
            else if (!$indexDiff->matches()) {
                $dbOperation = sprintf("Modify Index %s=>%s on table %s", $index->name(), $index->columns(), $table->name); 
                $table->alterIndex(AlterType::DROP, $index);
                $table->alterIndex(AlterType::ADD, $index);
            }
            //Add a DB operation if one happened
            if ($dbOperation !== null) self::addDBOperation($dbOperation);
        }
    }
}

?>