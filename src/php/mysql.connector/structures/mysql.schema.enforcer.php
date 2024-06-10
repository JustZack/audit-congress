<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        private Schema $schema;
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
            foreach ($this->schema->getTables() as $name=>$schemaObj) {
                $schemaTableNames[strtolower($name)] = true;
                //Enforce the known schema onto this table
                self::enforceTableSchema($schemaObj);
            }
            //Second pass to drop all tables not listed in the schema
            self::dropUnknownTables($schemaTableNames);
        }



        //For the given table $name, enforce the given $columns onto its schema
        private static function enforceTableSchema(TableSchema $schema) {
            //Create an object for this table
            $table = new Table($schema->getName());
            //If the table doesnt exist, create the table
            if (!$table->exists()) {
                $columnCreateSqlArr = $schema->getColumns()->getSqlCreateStrings();
                $table->create($columnCreateSqlArr);
                self::addDBOperation("Create Table $table->name");
            }
            //Otherwise enforce the schema for this table
            else self::enforceTableStructure(AlterStructure::COLUMN, $table, $schema->getColumns());
            //Always enforce indexes if present
            self::enforceTableStructure(AlterStructure::INDEX, $table, $schema->getIndexes());
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
                if ($structure == AlterStructure::COLUMN) $otherPart = $obj->type();
                else if ($structure == AlterStructure::INDEX) $otherPart = $obj->columns();
                //Actually add the db operation
                $dbOperation = sprintf($dbOperation, $obj->name(), $otherPart, $table->name); 
                self::addDBOperation($dbOperation);
            }
        }
    }
}

?>