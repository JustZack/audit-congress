<?php

namespace MySqlConnector {
    abstract class SqlObject {
        private 
            $columns = array(), 
            $values = array(),
            $selectColumns = array(),
            $tableName,
            $table;
        private $booleanConditon = "AND", $useLike = false;

        public function __construct($tableName, $booleanOperator = "AND", $useLike = false) {
            $this->tableName = $tableName;
            $this->table = new Table($this->tableName);

            $this->booleanConditon = $booleanOperator;
            $this->useLike = $useLike;
        }

        public function selectFromDB() { return $this->table->selectObject($this); }

        public function deleteFromDb() { return $this->table->deleteObject($this); }

        public function insertIntoDB() { return $this->table->insertObject($this); }

        public function updateInDb() { return $this->table->updateObject($this); }


        //Use AND to separate condtions
        public function useAnd() { $this->booleanConditon = "AND"; }
        //Use OR to separate condtions
        public function useOr() { $this->booleanConditon = "AND"; }
        //Use '=' sign to check equality
        public function useEquals() { $this->useLike = false; }
        //Use 'like' to check equality, also appending % to start and end of values 
        public function useLike() { $this->useLike = true; }

        //Check that the number of columns matches the number of values
        private function sameNumberOfColumnsAndValues() {
            return count($this->columns) == count($this->values);
        }
        //Return a SQL string with the propper `column` = 'value' syntax
        private static function getColumnEqualsValueSql($column, $value, $isLike) {
            if ($isLike) return sprintf("`%s` like '%s%s%s'", $column, "%", $value, "%");
            else         return sprintf("`%s` = '%s'", $column, $value);
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
                if (!empty($this->values[$i])) {
                    array_push($nonNullValues, $this->values[$i]);
                    array_push($nonNullColumns, $this->columns[$i]);
                }
            }

            return ["columns" => $nonNullColumns, "values" => $nonNullValues];
        }        
        //Return a condtion for this sql object with the set boolean operator and '=' or 'like'
        public function whereCondition() {
            $condition = "";
            
            if (!$this->sameNumberOfColumnsAndValues()) throw new \Exception("Column/Value set length mismatch");
            else {
                list("columns" => $columns, "values" => $values) = $this->getApplicableColumnsAndValues();
                //For each column
                $numColumns = count($columns);
                for ($i = 0;$i < $numColumns;$i++) {
                    //Set column = value sql string
                    $condition .= SqlObject::getColumnEqualsValueSql($columns[$i], $values[$i], $this->useLike);
                    if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                        $condition .= SqlObject::getLogicalOperatorSql($this->booleanConditon);
                }
            }
            var_dump($condition);
            return $condition;
        }

        //Get the columns used when selecting this object
        public function getSelectColumns() { return $this->selectColumns; }
        //Set the columns used when selecting  this object
        public function setSelectColumns(array $newColumns) { return $this->selectColumns = $newColumns; }

        //Get the columns provided to this object
        public function getColumns() { return $this->columns; }
        //Set the columns used by this object (for setting or updating values)
        public function setColumns(array $newColumns) { return $this->columns = $newColumns; }


        //Get the values provided to this object
        public function getValues() { return $this->values; }
        //Set the values used by this object
        public function setValues(array $newValues) { return $this->values = $newValues; }

        //Get the tablename used by this object
        public function getTableName() { return $this->tableName; }
        //Set the tablename used by this object
        public function setTableName($tableName) { $this->tableName = $tableName; }
    }
}

?>
