<?php

namespace MySqlConnector {
    abstract class SqlObject {
        private 
            $columns = array(), 
            $values = array(),
            $primaryKey = "";

        public function __construct($primaryKey, $columns, $values) {
            $this->primaryKey = $primaryKey;
            $this->columns = $columns;
            $this->values = $values;
        }

        public function getColumns() { return $this->columns; }
        public function setColumns(array $newColumns) { return $this->columns = $newColumns; }

        public function getValues() { return $this->values; }
        public function setValues(array $newValues) { return $this->values = $newValues; }
    }
}

?>