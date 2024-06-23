<?php

namespace MySqlConnector {
    abstract class QueryWrapper extends QueryOptions {
        protected Table $table;
        private 
            $booleanConditon = "AND", 
            $equalityOperator = false,
            $conditionList = array(), 
            $operatorList = array();

        public function __construct($tableName, $equalityOperator = "=", $booleanOperator = "AND") {
            $this->table = new Table($tableName);
            $this->setEqualityOperator($equalityOperator);
            $this->setBooleanCondition($booleanOperator);
        }

        public function getAsRow() { return SqlRow::fromColsAndVals($this->getColumns(), $this->getValues()); }

        public function countInDB() { return $this->table->count($this->whereCondition()); }

        public function selectFromDB() { return $this->table->selectObject($this); }

        public function deleteFromDb() { return $this->table->delete($this->whereCondition()); }

        public function insertIntoDB() { return $this->table->insert($this->getAsRow()); }

        public function updateInDb() { return $this->table->update($this->getAsRow(), $this->whereCondition()); }


        
        //Set the boolean operator to use in the WHERE condition
        public function setBooleanCondition($condition) {
            $condition = strtolower($condition);
            if (QueryBuilder::isAllowedConditional($condition))
                $this->booleanConditon = $condition;
            else self::throw("Tried using an unsupported condition '$condition'");
        }

        public function setBooleanConditions($conditionsArray) {
            foreach ($conditionsArray as $conditional) {
                $conditional = strtolower($conditional);
                if (!QueryBuilder::isAllowedConditional($conditional))
                    self::throw("Tried using an unsupported condition '$conditional'");
            }
            $this->conditionList = $conditionsArray;
        }
        
        public function getBooleanConditions() {
            if ($this->conditionList == null) {
                $numSearchCols = count($this->getSearchColumns());
                if ($numSearchCols > 1) return array_fill(0, $numSearchCols-1, $this->booleanConditon);
                else return [$this->booleanConditon];
            } else return $this->conditionList;
        }



        //Set the equality oeprator to use in the WHERE condition        
        public function setEqualityOperator($operator) {
            $operator = strtolower($operator);
            if (QueryBuilder::isAllowedOperator($operator))
                $this->equalityOperator = $operator;
            else self::throw("Tried using an unsupported operator '$operator'");
        }

        public function setEqualityOperators($operatorsArray) {
            foreach ($operatorsArray as $operator) {
                $operator = strtolower($operator);
                if (!QueryBuilder::isAllowedOperator($operator))
                    self::throw("Tried using an unsupported operator '$operator'");
            }
            $this->operatorList = $operatorsArray;
        }

        public function getEqualityOperators() {
            if ($this->operatorList == null) {
                return array_fill(0, count($this->getSearchColumns()), $this->equalityOperator);
            } else return $this->operatorList;
        }
       


        //Return a condtion for this sql object with the set boolean operator and '=' or 'like'
        public function whereCondition() {
            $condition = "";
            
            $sColumns = $this->getSearchColumns();
            $sValues = $this->getSearchValues();
            if (!QueryBuilder::sameNumberOfColumnsAndValues($sColumns, $sValues)) 
                self::throw("Column/Value set length mismatch");
            else 
                $condition = QueryBuilder::buildWhereCondition($sColumns, $sValues,
                                    $this->getEqualityOperators(), $this->getBooleanConditions());
            return $condition;
        }


        
        //Add a search value to the where condition
        //Requires: the $column name, $equalityOperator (=)
        public function addSearchValue($column, $equalityOperator, $value) {
            if (QueryBuilder::isAllowedOperator($equalityOperator)) {
                array_push($this->searchColumns, $column);
                array_push($this->operatorList, $equalityOperator);
                array_push($this->searchValues, $value);
            } else self::throw("Tried using an unsupported operator '$equalityOperator'");
        }
    }
}

?>
