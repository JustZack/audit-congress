<?php

namespace MySqlConnector {
    class Columns {
        
        private $columns = array();
        public function __construct($columnsArr) {
            foreach ($columnsArr as $column) {
                $colObj = new Column($column);
                $this->columns[$colObj->name] = $colObj;
            }
        }

        //Return the names for each column
        public function names() {
            return array_keys($this->columns);
        }
        //Return the name=>type for each column
        public function namesAndTypes() {
            $namesAndTypes = array();
            foreach ($this->columns as $name=>$column) $namesAndTypes[$name] = $column->getTypeString();
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

        /*
            Compare the columns in this object to the passed columns
                Uses 'this' object as truth, to decide what is 'wrong' with the other
            Returns an array where each element relates to every unique column in 'this' object and the other object
            Each object looks like so:
                "columnName" => ["type"=>"SQL Type Str",
                                 "exists"=>boolean, //Is the column shared?
                                 "matches"=>boolean, //Is the column type the same?
                                 "extra"=>boolean //Does this column only exist in $otherColumns?
                                ]
        */
        public function compareEach(Columns $otherColumns) {
            $columnDifferences = array();

            //First pass comparing each column in this object (expected columns) to those in the passed object
            foreach ($this->columns as $name=>$thisColumn) {
                //First try fetching this column from $otherColumns
                $otherColumn = $otherColumns->getColumn($name);
                //Check if it exists in $otherColumns
                $exists = $otherColumn != null;
                //Check if this column matches the $otherColumn
                $matches = $thisColumn->typeMatches($otherColumn);
                //Define this column in the $columnDifferences
                $columnDifferences[$name] = Columns::createColumnDifference($name, $thisColumn->getTypeString(), $exists, $matches, false);
            }

            //Second pass to catch any columns in the other object that dont exist in this one
            foreach ($otherColumns->columns as $name=>$otherColumn)
                //Only if this column isnt set in $columnDifferences already
                if (!isset($columnDifferences[$name]))
                    //Define this as an extra column in the $columnDifferences
                    $columnDifferences[$name] = Columns::createColumnDifference($name, $otherColumn->getTypeString(), false, false, true);

            return $columnDifferences;
        }

        private static function createColumnDifference($key, $type, $exists, $matches, $extra) {
            return array("type"=>$type, "exists"=>$exists, "matches"=>$matches, "extra"=>$extra);
        }
        
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
            $this->name = trim($obj[0]);
            $this->type = trim($obj[1]);
            $this->canBeNull = strtolower($obj[2]) == "no" ? "NOT NULL" : "NULL";
            $this->isPrimary = strtolower($obj[3]) == "pri";
            $this->defaultvalue = trim($obj[4]);
            $this->extra = trim($obj[5]);
        }

        //Return the type for this column, like "VARCHAR(50) NOT NULL"
        public function getTypeString() {
            return "$this->type $this->canBeNull";
        }

        public function getCreateString() {
            $type = $this->getTypeString();
            return "`$this->name` $type";
        }

        public function getPrimaryKeyString() {
            if ($this->isPrimary) return "PRIMARY KEY (`$this->name`)";
            else return "";
        }

        //Check if this column type matches the given columns type
        public function typeMatches($other) {
            if ($other == null) return false;

            $thisType = strtolower($this->getTypeString());
            $otherType = strtolower($other->getTypeString());
            return strpos($thisType, $otherType) > -1 || strpos($otherType, $thisType) > -1;
        }
    } 
}

?>