<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        public $schema = null;
        private static $debug_log = true;
        public function __construct($schemaFilePath) {
            $this->schema = json_decode(file_get_contents($schemaFilePath), true);
            $this->enforceSchema();
        }

        public static function debugPrint($message) {
            if (SchemaEnforcer::$debug_log && strlen($message) > 0) 
                echo $message;
        }

        public function enforceSchema() {
            $schemaTableNames = array();
            //First pass to iterate over tables that should exist in the schema
            foreach ($this->schema["tables"] as $tableSchema) {
                //Fetch the name, columns, and a Columns object for this table in the schema
                $name = $tableSchema["name"];
                $columns = $tableSchema["columns"];
                //Pushback this table name as a known table;
                $schemaTableNames[strtolower($name)] = true;

                //Get the schemas columns in the form of a \MySqlConnector\Columns object
                $columnsExpected = SchemaEnforcer::getSchemaColumnsAsObject($columns);
                //Create an object for this table
                $table = new Table($name);

                //If the table doesnt exist, create the table
                if (!$table->exists()) {
                    $table->create($columnsExpected->getSqlCreateStrings());
                    SchemaEnforcer::debugPrint("Create Table $name");
                }
                //Otherwise enforce the schema for this table
                else SchemaEnforcer::enforceColumnSchema($table, $columnsExpected, $table->columns());
            }

            //Get all tables in the database (according to the current connection)
            $knownTables = Table::showTables();
            //Second pass to drop tables that should not exist
            foreach ($knownTables as $name)
                if (!isset($schemaTableNames[strtolower($name)])) {
                    $table = new Table($name);
                    $table->drop();
                    SchemaEnforcer::debugPrint("Drop Table $name\n");
                }
        }

        public static function enforceColumnSchema($table, $columnsExpected, $columnsExisting) {
            $columnsDiff = $columnsExpected->compareEach($columnsExisting);
            foreach ($columnsDiff as $name=>$data) {
                $type = $data["type"];
                $exists = $data["exists"];
                $matches = $data["matches"];
                $extra = $data["extra"];

                $debug_message = "";
                //Drop extra columns
                if ($extra) { $table->dropColumn($name, $type);  $debug_message = "Drop $name=>$type\n"; }
                //Add missing columns
                else if (!$exists) { $table->addColumn($name, $type);  $debug_message = "Add $name=>$type\n"; }
                //Modify column mismatches
                else if (!$matches) { $table->modifyColumn($name, $type); $debug_message = "Modify $name=>$type\n"; }

                SchemaEnforcer::debugPrint($debug_message);
            }
        }

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