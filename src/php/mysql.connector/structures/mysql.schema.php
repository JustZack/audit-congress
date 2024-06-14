<?php

namespace MySqlConnector {
    
    //Wrapper that makes it easy to interact with the expected database schema
    class Schema extends \Util\Schema {
        private $tables = array();

        public function __construct($rawSchemaJSON) {
            parent::__construct($rawSchemaJSON);
            foreach ($this->raw()["tables"] as $table) 
                $this->tables[$table["name"]] = new TableSchema($table);
        }

        public function getVersion() { return $this->raw()["version"]; }

        public function getTables() { return $this->tables; }

        public function listTables() { return array_keys($this->tables); }

        public function getTable($tableName) : TableSchema { return $this->tables[$tableName]; }
    }
}

?>