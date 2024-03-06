<?php

namespace MySqlConnector {
    abstract class SqlObject {
        private
            $selectColumns = array("*"),
            $searchColumns = array(), 
            $searchValues = array(),  
            $columns = array(), 
            $values = array();
        protected Table $table;
        private $booleanConditon = "AND", $equalityOperator = false;

        public function __construct($tableName, $equalityOperator = "=", $booleanOperator = "AND") {
            $this->table = new Table($tableName);
            $this->setEqualityOperator($equalityOperator);
            $this->setConditionOperator($booleanOperator);
        }

        public function selectFromDB() { 
            return $this->table->select($this->getSelectColumns(), $this->whereCondition());
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

        //Set the boolean operator to use in the WHERE condition
        public function setConditionOperator($condition) {
            $condition = strtolower($condition);
            if (in_array($condition, Query::$allowedConditions))
                $this->booleanConditon = $condition;
            else throw new SqlException("SQLObject: Tried using an unsupported condition '$condition'");
        }
        //Set the equality oeprator to use in the WHERE condition        
        public function setEqualityOperator($operator) {
            $operator = strtolower($operator);
            if (in_array($operator, Query::$allowedOperators))
                $this->equalityOperator = $operator;
            else throw new SqlException("SQLObject: Tried using an unsupported operator '$operator'");
        }
       
        //Return a condtion for this sql object with the set boolean operator and '=' or 'like'
        public function whereCondition() {
            $condition = "";
            
            $sColumns = $this->getSearchColumns();
            $sValues = $this->getSearchValues();
            if (!QueryBuilder::sameNumberOfColumnsAndValues($sColumns, $sValues)) 
                throw new SqlException("SqlObject: Column/Value set length mismatch");
            else 
                $condition = QueryBuilder::buildWhereCondition($sColumns, $sValues,
                                    $this->equalityOperator, $this->booleanConditon);
            return $condition;
        }

        //Get the columns used when selecting this object
        public function getSelectColumns() { return $this->selectColumns; }
        //Set the columns used when selecting  this object
        public function setSelectColumns(array $newColumns) { return $this->selectColumns = $newColumns; }

        //Get the columns used when searching for this object
        public function getSearchColumns() { return $this->searchColumns; }
        //Set the columns used when searching for this object
        public function setSearchColumns(array $newColumns) { return $this->searchColumns = $newColumns; }

        //Get the values provided to this object
        public function getSearchValues() { return $this->searchValues; }
        //Set the values used by this object
        public function setSearchValues(array $newValues) { return $this->searchValues = $newValues; }

        //Get the columns provided to this object
        public function getColumns() { return $this->columns; }
        //Set the columns used by this object (for setting or updating values)
        public function setColumns(array $newColumns) { return $this->columns = $newColumns; }

        //Get the values provided to this object
        public function getValues() { return $this->values; }
        //Set the values used by this object
        public function setValues(array $newValues) { return $this->values = $newValues; }
    }
}

?>
