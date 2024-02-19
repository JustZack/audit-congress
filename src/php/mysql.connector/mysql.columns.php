<?php

namespace MySqlConnector {
    class Columns {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column)
                array_push($this->columns, new Column($column));
        }

        public function columnCanBeNull($column) {
            $cols = $this->columns;
            for ($i = 0;$i < count($this->columns);$i++) 
                if ($cols[$i]->name == $column)
                    return $cols[$i]->canBeNull;
        }
        public function columnIsPrimary($column) {
            $cols = $this->columns;
            for ($i = 0;$i < count($this->columns);$i++) 
                if ($cols[$i]->keyType == "PRI")
                    return $cols[$i]->canBeNull;
        }

        public function names() {
            $names = array();
            $cols = $this->columns;
            for ($i = 0;$i < count($cols);$i++) 
                array_push($names, $cols[$i]->name);
            return $names;
        }
        public function namesAndTypes() {
            $namesAndTypes = array();
            $cols = $this->columns;
            for ($i = 0;$i < count($cols);$i++) 
                $namesAndTypes[$cols[$i]->name] = $cols[$i]->type;
            return $namesAndTypes;
        }
        public function list() { return $this->columns; }
    }

    class Column {
        public 
            $name,
            $type,
            $canBeNull,
            $defaultvalue,
            $extra;

        public function __construct($obj) {
            $this->name = $obj[0];
            $this->type = $obj[1];
            $this->canBeNull = $obj[2] = "YES" ? true : false;
            $this->defaultvalue = $obj[4];
            $this->extra = $obj[5];
        }
    } 
}

?>