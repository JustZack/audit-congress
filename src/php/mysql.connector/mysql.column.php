<?php

namespace MySqlConnector {
    class Columns {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column)
                array_push($this->columns, new Column($column));
        }

        public function items() { return $this->columns; }
    }

    class Column {
        public 
            $name,
            $type,
            $canBeNull,
            $keyType,
            $defaultvalue,
            $extra;

        public function __construct($obj) {
            $this->name = $obj[0];
            $this->type = $obj[1];
            $this->canBeNull = $obj[2];
            $this->keyType = $obj[3];
            $this->defaultvalue = $obj[4];
            $this->extra = $obj[5];
        }
    } 
}

?>