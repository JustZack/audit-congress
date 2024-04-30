<?php

namespace MySqlConnector {
    class Indexes {

        private $indexes = array();

        public function __construct($indexesArr) {
            foreach ($indexesArr as $indexRow) {
                $indexObj = new Index($indexRow);
                //If the index described by the given row already exists
                if ($this->hasIndexName($indexObj->name)) //Append it to the existing one (this is a multirow index)
                    $this->getIndex($indexObj->name)->addColumn($indexObj->columns[0]);
                else if (!$indexObj->isPrimary()) //Otherwise only add non primary key indexes
                    $this->indexes[$indexObj->name] = $indexObj;
            }
        }
        //Return the index described by the given name
        public function getIndex($name) {
            if ($this->hasIndexName($name)) return $this->indexes[$name];
            else return null;
        }
        //Check if the given index name is known
        public function hasIndexName($name) {
            return array_key_exists($name, $this->indexes);
        }
        //Check if the given index name & columns are known
        public function hasIndex($name, $columns) {
            if ($this->hasIndexName($name)) {
                $index = $this->getIndex($name);
                if ($index->columns == $columns) return true;
            }
            return false;
        }
        //Count how many indexes are tracked by this object
        public function count() { return count($this->indexes); }

        public function compareEach(Indexes $otherIndexes) {
            $indexDifferences = array();

            //First pass comparing each column in this object (expected columns) to those in the passed object
            foreach ($this->indexes as $name=>$thisIndex) {
                //First try fetching this column from $otherColumns
                $otherIndex = $otherIndexes->getIndex($name);
                //Check if it exists in $otherColumns
                $exists = $otherIndex != null;
                //Check if this column matches the $otherColumn
                $matches = $thisIndex->columnsMatch($otherIndex);
                //Define this column in the $columnDifferences
                $indexDifferences[$name] = self::createIndexDifference($thisIndex->columns, $exists, $matches, false);
            }

            //Second pass to catch any columns in the other object that dont exist in this one
            foreach ($otherIndexes->indexes as $name=>$otherIndex)
                //Only if this column isnt set in $columnDifferences already
                if (!isset($indexDifferences[$name]))
                    //Define this as an extra column in the $columnDifferences
                    $indexDifferences[$name] = self::createIndexDifference($otherIndex->columns, false, false, true);

            return $indexDifferences;
        }

        private static function createIndexDifference($columns, $exists, $matches, $extra) {
            return array("columns"=>$columns, "exists"=>$exists, "matches"=>$matches, "extra"=>$extra);
        }

    }

    class Index {
        public
            $nonUnique,
            $name,
            $columns = array(),
            $collation,
            $cardinality,
            $type,
            $visible;
        public function __construct($indexRow) {
            $this->nonUnique = $indexRow[1];
            $this->name = $indexRow[2];
            $this->addColumn($indexRow[4]);
            $this->collation = $indexRow[5];
            $this->cardinality = $indexRow[6];
            $this->type = $indexRow[10];
            $this->visible = $indexRow[13];
        }

        public function addColumn($column) {
            array_push($this->columns, strtolower($column));
        }

        public function isPrimary() {
            return $this->name == "PRIMARY";
        }

        public function columnsMatch($otherIndex) {
            return $this->columns == $otherIndex->columns;
        }
    }
}

?>