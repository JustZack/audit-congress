<?php

namespace MySqlConnector {
    class Columns extends CompareableSet {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column) {
                $colObj = new Column($column);
                $this->set($colObj->name, $colObj);
            }
        }

        //Return the names for each column
        public function names() {
            return array_keys($this->columns);
        }
        //Return the name=>type for each column
        public function namesAndTypes() {
            $namesAndTypes = array();
            foreach ($this->columns as $name=>$column) $namesAndTypes[$name] = $column->type();
            return $namesAndTypes;
        }
        //Return an array of strings used to create these columns
        public function getSqlCreateStrings() {
            $createStrings = array();
            foreach ($this->columns as $name=>$thisColumn) {
                array_push($createStrings, $thisColumn->getCreateString());
                if ($thisColumn->isPrimary) array_push($createStrings, $thisColumn->getPrimaryKeyString());
            }
            return $createStrings;
        }


        //Return all the columns as an array
        public function getColumns() {
            $array_columns = array();
            foreach ($this->columns as $name=>$column) array_push($array_columns, $column); 
            return $array_columns;
        }
        //Return the column given by $name
        public function getColumn($name) { 
            if (isset($this->columns[$name])) return $this->columns[$name]; 
            else return null;
        }
        //Check if the given column is null
        public function columnCanBeNull($name) {
            $column = $this->getColumn($name);
            if ($column != null) return $column->canBeNull == "NULL";
            else throw new SqlException("Tried checking if column $name can be null, but $name doesnt exist in this column set.");
        }
    }

    class Column extends CompareableObject {
        public 
            $name,
            $type,
            $isPrimary,
            $canBeNull,
            $defaultvalue,
            $extra;

        public function __construct($obj) {
            $this->name = trim($obj[0]);
            $this->type = trim($obj[1]);
            $this->canBeNull = strtolower($obj[2]) == "no" ? "NOT NULL" : "NULL";
            $this->isPrimary = strtolower($obj[3]) == "pri";
            $this->defaultvalue = trim($obj[4]);
            $this->extra = trim($obj[5]);
        }

        public function getCreateString() {
            $type = $this->type();
            return "`$this->name` $type";
        }

        public function getPrimaryKeyString() {
            if ($this->isPrimary) return "PRIMARY KEY (`$this->name`)";
            else return "";
        }

        //Check if this column type matches the given columns type
        public function matches($other) {
            if ($other == null) return false;

            $thisType = $this->type();
            $otherType = $other->type();
            return strpos($thisType, $otherType) > -1 || strpos($otherType, $thisType) > -1;
        }

        public function name() { return $this->name; }

        //Return the type for this column, like "VARCHAR(50) NOT NULL"
        public function type() {
            return strtolower("$this->type $this->canBeNull $this->extra");
        }
    } 
}

?>