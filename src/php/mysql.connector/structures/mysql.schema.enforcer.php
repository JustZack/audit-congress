<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        public $schema = null;
        private static $debug_log = false;
        private static $operations = array();

        public function __construct($schema) {
            $this->schema = $schema;
        }
        //Print only if $debug_log is true
        private static function debugPrint($message) {
            if (self::$debug_log && strlen($message) > 0) 
                echo $message."\n";
        }
        //Append an operation to the DB
        private static function addDBOperation($operation) { 
            self::debugPrint($operation);
            array_push(self::$operations, $operation); 
        }
        //Return all operations this object has done to the DB
        public static function getDBOperationsList() { return self::$operations; }



        //Do all the enforcement on the database described the the connection
        public function enforceSchema() {
            $schemaTableNames = array();
            //First pass to iterate over tables that should exist in the schema
            foreach ($this->schema["tables"] as $tableSchema) {
                //Fetch the name, columns, and a Columns object for this table in the schema
                list("name"=>$name, "columns"=>$schemaColumns) = $tableSchema;
                //Indexes are not required, but fetch if they do exist
                $schemaIndexes = array_key_exists("indexes", $tableSchema) ? $tableSchema["indexes"] : array();
                //Add this table name as a known table
                $schemaTableNames[strtolower($name)] = true;
                //Create an object for this table
                $table = new Table($name);
                //Enforce the known schema onto this table
                self::enforceTableSchema($table, $schemaColumns, $schemaIndexes);
            }
            //Second pass to drop all tables not listed in the schema
            self::dropUnknownTables($schemaTableNames);
        }



        //For the given table $name, enforce the given $columns onto its schema
        private static function enforceTableSchema($table, $schemaColumns, $schemaIndexes) {
            //Get the schemas columns in the form of a \MySqlConnector\Columns object
            $columnsExpected = self::getSchemaColumnsAsObject($schemaColumns);
            //Get the schemas indexes in the form of a \MySqlConnector\Indexes object
            $indexesExpected = self::getSchemaIndexesAsObject($schemaIndexes);

            //If the table doesnt exist, create the table
            if (!$table->exists()) {
                $columnCreateSqlArr = $columnsExpected->getSqlCreateStrings();
                $table->create($columnCreateSqlArr);
                self::addDBOperation("Create Table $table->name");
            }
            //Otherwise enforce the schema for this table
            else self::enforceTableStructure(AlterStructure::COLUMN, $table, $columnsExpected);

            //Always enforce indexes
            self::enforceTableStructure(AlterStructure::INDEX, $table, $indexesExpected);
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
        //Get the given $schemaIndexes as an Indexes object, which is then used to enforce schema
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
        


        //Generically enforce table structures based on the $structure (COLUMNS, INDEX) provided
        private static function enforceTableStructure($structure, $table, $expected) {
            //Fetch existing indexes
            $existing = null;
            if ($structure == AlterStructure::COLUMN) $existing = $table->columns();
            else if ($structure == AlterStructure::INDEX) $existing = $table->indexes();
            //Compute the difference between them
            $diff = $expected->compare($existing);
            //Decide what to do with each difference
            foreach ($diff as $name=>$objDiff) //Broken into handler to simplify
                self::handleEnforceSchemaStructure($structure, $table, $objDiff);
        }
        //Generically handle what to do with each difference found between the existing and expected structure
        private static function handleEnforceSchemaStructure($structure, $table, $objectDiff) {
            $obj = $objectDiff->item();
            $dbOperation = null;

            //Drop extra
            if ($objectDiff->extra()) {
                $dbOperation = "Drop $structure %s=>%s from table %s";
                $table->alter($structure, AlterType::DROP, $obj);
            }
            //Add missing
            else if (!$objectDiff->exists()) {
                $dbOperation = "Add $structure %s=>%s to table %s";
                $table->alter($structure, AlterType::ADD, $obj);
            }
            //Modify mismatch
            else if (!$objectDiff->matches()) {
                $dbOperation = "Modify $structure %s=>%s on table %s";
                $table->alter($structure, AlterType::MODIFY, $obj);
            }
            //Add a DB operation if one happened
            if ($dbOperation !== null) {
                //Determine what the other side of the => should have
                $otherPart = "";
                if ($obj instanceof Column) $otherPart = $obj->type();
                else if ($obj instanceof Index) $otherPart = $obj->columns();
                //Actually add the db operation
                $dbOperation = sprintf($dbOperation, $obj->name(), $otherPart, $table->name); 
                self::addDBOperation($dbOperation);
            }
        }
    }
}

?>