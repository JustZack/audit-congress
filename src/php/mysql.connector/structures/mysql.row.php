<?php

namespace MySqlConnector {
    class SqlRow {
        protected
            $columns = [],
            $values = [];
        public function __construct($rowAssocArray = null, $setAsArrays = false) {
            if ($setAsArrays) $this->setArraysFromAssoc($rowAssocArray);
            else $this->setFieldsFromAssoc($rowAssocArray);    
        }

        public static function fromColsAndVals($columns, $values) {
            $rowAssoc = array();
            for ($i = 0;$i < count($columns);$i++) $rowAssoc[$columns[$i]] = $values[$i];
            return new static($rowAssoc, true);
        }

        public function getColumns() { return $this->columns; }
        public function getValues() { return $this->values; }

        private function setFieldsFromAssoc($obj, $keyMustExist = true) {
            foreach ($obj as $key=>$value)
                if (($keyMustExist && property_exists($this, $key)) || !$keyMustExist) 
                    $this->{$key} = $value;
        }

        private function setArraysFromAssoc($obj) {
            foreach ($obj as $key=>$value) {
                array_push($this->columns, $key);
                array_push($this->values, $value);
            }
        }

        
        public static function rowsToObjects($rows) {
            $rowObjects = array();
            foreach ($rows as $row) array_push($rowObjects, new static($row));
            return $rowObjects;
        }
    }
}

?>