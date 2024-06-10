<?php

namespace MySqlConnector {
    
    //Wrapper that makes it easy to interact with the expected database schema
    class Schema {
        private 
            $rawSchema,
            $tables = array();

        public function __construct($rawSchemaJSON) {
            $this->rawSchema = $rawSchemaJSON;
            foreach ($this->rawSchema["tables"] as $table) 
                $this->tables[$table["name"]] = new TableSchema($table);
        }


        public function getName() { return $this->rawSchema["name"]; }

        public function getVersion() { return $this->rawSchema["version"]; }

        public function getTables() { return $this->tables; }

        public function listTables() { return array_keys($this->tables); }

        public function getTable($tableName) : TableSchema { return $this->tables[$tableName]; }
    }
}

?>