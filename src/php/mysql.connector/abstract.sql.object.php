<?php

namespace MySqlConnector {
    abstract class SqlObject extends QueryOptions {
        protected Table $table;
        private 
            $booleanConditon = "AND", 
            $equalityOperator = false,
            $conditionList = null, 
            $operatorList = null;

        public function __construct($tableName, $equalityOperator = "=", $booleanOperator = "AND") {
            $this->table = new Table($tableName);
            $this->setEqualityOperator($equalityOperator);
            $this->setBooleanCondition($booleanOperator);
        }

        public function selectFromDB() { 
            return $this->table->select($this->getSelectColumns(), $this->whereCondition(),
                                        $this->getJoin(), $this->getGroupBy(),
                                        $this->getOrderBy(), $this->getLimit());
        }

        public function deleteFromDb() { 
            return $this->table->delete($this->whereCondition());
        }

        public function insertIntoDB() { 
            return $this->table->insert($this->getColumns(), $this->getValues());

        }

        public function updateInDb() { 
            return $this->table->update($this->getColumns(), $this->getValues(), 
                                        $this->whereCondition());
        }

        public static function throwSqlObjectError($message) {
            throw new SqlException("SqlObject: $message");
        }



        //Set the boolean operator to use in the WHERE condition
        public function setBooleanCondition($condition) {
            $condition = strtolower($condition);
            if (QueryBuilder::isAllowedConditional($condition))
                $this->booleanConditon = $condition;
            else self::throwSqlObjectError("Tried using an unsupported condition '$condition'");
        }

        public function setBooleanConditions($conditionsArray) {
            foreach ($conditionsArray as $conditional) {
                $conditional = strtolower($conditional);
                if (!QueryBuilder::isAllowedConditional($conditional))
                    self::throwSqlObjectError("Tried using an unsupported condition '$conditional'");
            }
            $this->conditionList = $conditionsArray;
        }
        
        public function getBooleanConditions() {
            if ($this->conditionList == null) {
                return array_fill(0, count($this->getSearchColumns())-1, $this->booleanConditon);
            } else return $this->conditionList;
        }



        //Set the equality oeprator to use in the WHERE condition        
        public function setEqualityOperator($operator) {
            $operator = strtolower($operator);
            if (QueryBuilder::isAllowedOperator($operator))
                $this->equalityOperator = $operator;
            else self::throwSqlObjectError("Tried using an unsupported operator '$operator'");
        }

        public function setEqualityOperators($operatorsArray) {
            foreach ($operatorsArray as $operator) {
                $operator = strtolower($operator);
                if (!QueryBuilder::isAllowedOperator($operator))
                    self::throwSqlObjectError("Tried using an unsupported operator '$operator'");
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
                self::throwSqlObjectError("Column/Value set length mismatch");
            else 
                $condition = QueryBuilder::buildWhereCondition($sColumns, $sValues,
                                    $this->getEqualityOperators(), $this->getBooleanConditions());
            return $condition;
        }
    }
}

?>
