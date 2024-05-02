<?php

namespace MySqlConnector {
    class Columns extends CompareableSet {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column) $this->add(new Column($column));
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
    }

    class Column extends CompareableObject {
        public 
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

        //Return the type for this column, like "VARCHAR(50) NOT NULL"
        public function type() {
            return strtolower("$this->type $this->canBeNull $this->extra");
        }
    } 
}

?>