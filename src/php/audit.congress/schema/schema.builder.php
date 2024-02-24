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
                $table = new \MySqlConnector\Table($name);
                //If the table doesnt exist, we can simply create the table
                if (!$table->exists()) $table->create($columns);
                //Otherwise we need to ensure the schema is being followed by this table
                else {
                    $existingColumns = SchemaBuilder::existingColumnsAssoc(SchemaBuilder::getColumns($name));
                    //$existingColumns = SchemaBuilder::getColumnsFromSchema($database, $name);
                    $expectedColumns = SchemaBuilder::expectedColumnsAssoc($columns);
                    $matchingColumns = SchemaBuilder::columnMatches($expectedColumns, $existingColumns);
                    
                    foreach ($matchingColumns as $name=>$column) {
                        $type = $column["type"];
                        if ($column["matches"] == false) {
                            var_dump("Will update $name to $type");
                            if ($column["exists"]) $table->modifyColumn($name, $type);
                            else                   $table->addColumn($name, $type);
                        } else {
                            var_dump("$name is already $type");
                        }
                    }
                }
            }
        }

        public static function expectedColumnsAssoc($schemaColumns) {
            $assocColunns = array();
            foreach ($schemaColumns as $index=>$column) {
                $splitItems = explode(" ", $column, 2);
                $name = strtolower(str_replace("`", "", $splitItems[0]));
                $type = strtolower($splitItems[1]);
                if ($name != "primary") $assocColunns[$name] = $type;
            }
            return $assocColunns;
        }

        public static function existingColumnsAssoc($schemaColumns) {
            $assocColunns = array();
            foreach ($schemaColumns as $index=>$column) {
                $name = strtolower($column[0]);
                $null = $column[2] == "NO" ? "NOT NULL" : "NULL";
                $type = strtolower($column[1]." $null");
                $assocColunns[$name] = $type;
            }
            return $assocColunns;
        }

        public static function getColumns($table) {            
            $descQuery = \MySqlConnector\Query::describe($table);
            $descriptions = $descQuery->execute();
            return $descriptions->fetchAll();
        }

        public static function getColumnsFromSchema($database, $table) {            
            \MySqlConnector\Connection::useDatabase("INFORMATION_SCHEMA");
            $infoSchema = new \MySqlConnector\Table("COLUMNS");
            $descriptions = $infoSchema->select(["*"], "TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'");
            \MySqlConnector\Connection::useDatabase($database);
            return $descriptions->fetchAll();
        }

        public static function columnMatches($expectedColumns, $existingColumns) {
            $matchingColumns = array();
            //var_dump($expectedColumns);
            //var_dump($existingColumns);
            foreach ($expectedColumns as $name=>$expectedDesc) {
                $existingDesc = null;
                $matchingColumns[$name] = array();
                $matchingColumns[$name]["type"] = $expectedDesc;
                $matchingColumns[$name]["exists"] = true;
                if (isset($existingColumns[$name])) {
                    $existingDesc = $existingColumns[$name];
                    //Check that expected OR existing column exists in the other
                    if (strpos($existingDesc, $expectedDesc) > -1 || strpos($expectedDesc, $existingDesc) > -1) {
                        $matchingColumns[$name]["matches"] = true;
                    }
                    else  $matchingColumns[$name]["matches"] = false;
                } else {
                    $matchingColumns[$name]["matches"] = false;
                    $matchingColumns[$name]["exists"] = false;
                }
            }
            return $matchingColumns;
        }
    }
}

?>