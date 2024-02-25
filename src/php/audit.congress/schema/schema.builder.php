<?php

namespace AuditCongress {

    class SchemaBuilder {
        public $schema = null;
        public function __construct($schemaFilePath = AUDITCONGRESS_FOLDER."\schema\schema.json") {
            $this->schema = json_decode(file_get_contents($schemaFilePath), true);
            $this->enforceSchema();
        }

        public function enforceSchema() {
            $database = \MySqlConnector\Connection::getDatabase();
            foreach ($this->schema["tables"] as $tableSchema) {
                $name = $tableSchema["name"];
                $columns = $tableSchema["columns"];
                $columnsExpected = SchemaBuilder::getSchemaColumnsAsObject($columns);
                $table = new \MySqlConnector\Table($name);

                //If the table doesnt exist, we can simply create the table
                if (!$table->exists()) $table->create($columnsExpected->getSqlCreateStrings());
                //Otherwise we need to ensure the schema is being followed by this table
                else {
                    $columnsExisting = $table->columns();
                    
                    SchemaBuilder::enforceColumnSchema($table, $columnsExpected, $columnsExisting);
                }
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
                if ($extra) $table->dropColumn($name, $type);
                //Add missing columns
                else if (!$exists) $table->addColumn($name, $type);
                //Modify column mismatches
                else if (!$matches) $table->modifyColumn($name, $type);
            }
        }

        public static function getSchemaColumnsAsObject($schemaColumns) : \MySqlConnector\Columns {
            $columnsInDescribeFormat = array();
            foreach ($schemaColumns as $name=>$data) {
                $primary = isset($data["primary"]) ? "PRI" : "";
                $column = array($name, $data["type"], $data["null"], $primary, "", "");
                array_push($columnsInDescribeFormat, $column);
            }
            return new \MySqlConnector\Columns($columnsInDescribeFormat);
        }
    }
}

?>