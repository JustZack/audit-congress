<?php

namespace MySqlConnector {

    class SchemaEnforcer {
        public $schema = null;
        public function __construct($schemaFilePath) {
            $this->schema = json_decode(file_get_contents($schemaFilePath), true);
            $this->enforceSchema();
        }

        public function enforceSchema() {
            foreach ($this->schema["tables"] as $tableSchema) {
                //Fetch the name, columns, and a Columns object for this table in the schema
                $name = $tableSchema["name"];
                $columns = $tableSchema["columns"];
                //Get the schemas columns in the form of a \MySqlConnector\Columns object
                $columnsExpected = SchemaEnforcer::getSchemaColumnsAsObject($columns);
                //Create an object for this table
                $table = new Table($name);

                //If the table doesnt exist, create the table
                if (!$table->exists()) $table->create($columnsExpected->getSqlCreateStrings());
                //Otherwise enforce the schema for this table
                else SchemaEnforcer::enforceColumnSchema($table, $columnsExpected, $table->columns());
            }
        }

        public static function enforceColumnSchema($table, $columnsExpected, $columnsExisting) {
            $columnsDiff = $columnsExpected->compareEach($columnsExisting);
            foreach ($columnsDiff as $name=>$data) {
                $type = $data["type"];
                $exists = $data["exists"];
                $matches = $data["matches"];
                $extra = $data["extra"];

                //Drop extra columns
                if ($extra) { $table->dropColumn($name, $type);  echo "Drop $name=>$type\n"; }
                //Add missing columns
                else if (!$exists) { $table->addColumn($name, $type);  echo "Add $name=>$type\n"; }
                //Modify column mismatches
                else if (!$matches) { $table->modifyColumn($name, $type); echo "Modify $name=>$type\n"; }
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