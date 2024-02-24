<?php

namespace MySqlConnector {
    class Columns {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column)
                array_push($this->columns, new Column($column));
        }



        //Check if the given column is null
        public function columnCanBeNull($column) {
            $cols = $this->columns;
            for ($i = 0;$i < count($this->columns);$i++) 
                if ($cols[$i]->name == $column)
                    return $cols[$i]->canBeNull;
        }



        //Return the names for each column
        public function names() {
            $names = array();
            $cols = $this->columns;
            for ($i = 0;$i < count($cols);$i++) 
                array_push($names, $cols[$i]->name);
            return $names;
        }
        //Return the name=>type for each column
        public function namesAndTypes() {
            $namesAndTypes = array();
            $cols = $this->columns;
            for ($i = 0;$i < count($cols);$i++) 
                $namesAndTypes[$cols[$i]->name] = $cols[$i]->getTypeString();
            return $namesAndTypes;
        }
        //Return the column objects
        public function list() { return $this->columns; }
    }

    class Column {
        public 
            $name,
            $type,
            $isPrimary,
            $canBeNull,
            $defaultvalue,
            $extra;

        public function __construct($obj) {
            $this->name = $obj[0];
            $this->type = $obj[1];
            $this->canBeNull = $obj[2] = "NO" ? "NOT NULL" : "NULL";
            $this->isPrimary = $obj[3] == "PRI";
            $this->defaultvalue = $obj[4];
            $this->extra = $obj[5];
        }

        public function getTypeString() {
            return "$this->type $this->canBeNull";
        }
    } 
}

?>