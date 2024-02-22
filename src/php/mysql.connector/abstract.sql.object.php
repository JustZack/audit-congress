<?php

namespace MySqlConnector {
    abstract class SqlObject {
        private 
            $columns = array(), 
            $values = array(),
            $selectColumns = array();

        //Get and setup an object meant to select a row in SQL
        abstract public static function getSelectObject($values);
        //Get and setup an object meant to manipulate a row in SQL
        abstract public static function getManipulateObject($values);
        //Generate a condition that matches to this object
        abstract public function whereCondition();

        //Check that the number of columns matches the number of values
        private function sameNumberOfColumnsAndValues() {
            return count($this->columns) == count($this->values);
        }
        //Return a SQL string with the propper `column` = 'value' syntax
        private static function getColumnEqualsValueSql($column, $value) {
            return sprintf("`%s` = '%s'", $column, $value);
        }
        //Return a SQL string containing the given logical operator
        private static function getLogicalOperatorSql($operator) {
            return sprintf(" %s ", $operator);
        }
        //Get all columns and values with a non null value
        private function getApplicableColumnsAndValues() {
            $nonNullValues = array();
            $nonNullColumns = array();

            for ($i = 0;$i < count($this->columns);$i++) {
                if ($this->values[$i] != null) {
                    array_push($nonNullValues, $this->values[$i]);
                    array_push($nonNullColumns, $this->columns[$i]);
                }
            }

            return ["columns" => $nonNullColumns, "values" => $nonNullValues];
        }        
        //Build a condtion with the provided operator, only including non null vaues
        private function buildCondition($operator) {
            $condition = "";
            
            if (!$this->sameNumberOfColumnsAndValues()) throw new \Exception("Column/Value set length mismatch");
            else {
                list("columns" => $columns, "values" => $values) = $this->getApplicableColumnsAndValues();
                //For each column
                $numColumns = count($columns);
                for ($i = 0;$i < $numColumns;$i++) {
                    //Set column = value sql string
                    $condition .= SqlObject::getColumnEqualsValueSql($columns[$i], $values[$i]);
                    if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                        $condition .= SqlObject::getLogicalOperatorSql($operator);
                }
            }

            return $condition;
        }

        //Build a condition ANDing each non null column/value pair
        public function buildANDConditon() { return $this->buildCondition("AND"); }
        //Build a condition ORing each non null column/value pair
        public function buildORConditon() { return $this->buildCondition("OR"); }

        //Get the columns used when selecting this object
        public function getSelectColumns() { return $this->selectColumns; }
        //Set the columns used when selecting  this object
        public function setSelectColumns(array $newColumns) { return $this->selectColumns = $newColumns; }

        //Get the columns provided to this object
        public function getColumns() { return $this->columns; }
        //Set the columns used by this object
        public function setColumns(array $newColumns) { return $this->columns = $newColumns; }


        //Get the values provided to this object
        public function getValues() { return $this->values; }
        //Set the values used by this object
        public function setValues(array $newValues) { return $this->values = $newValues; }
    }
}

?>