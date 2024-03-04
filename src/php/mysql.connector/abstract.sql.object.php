<?php

namespace MySqlConnector {
    abstract class SqlObject {
        private
            $selectColumns = array("*"),
            $searchColumns = array(), 
            $searchValues = array(),  
            $columns = array(), 
            $values = array(),
            $tableName;
        protected Table $table;
        private $booleanConditon = "AND", $equalityOperator = false;

        public function __construct($tableName, $equalityOperator = "=", $booleanOperator = "AND") {
            $this->setTableName($tableName);
            $this->equalityOperator = $equalityOperator;
            $this->booleanConditon = $booleanOperator;
        }

        public function selectFromDB() { return $this->table->selectObject($this); }

        public function deleteFromDb() { return $this->table->deleteObject($this); }

        public function insertIntoDB() { return $this->table->insertObject($this); }

        public function updateInDb() { return $this->table->updateObject($this); }

        //Use AND to separate condtions
        public function useAnd() { $this->booleanConditon = "AND"; }
        //Use OR to separate condtions
        public function useOr() { $this->booleanConditon = "OR"; }

        //Set the equality oeprator to use in the WHERE condition        
        public function setEqualityOperator($operator) {
            $operator = strtolower($operator);
            if (in_array($operator, Query::$allowedOperators))
                $this->equalityOperator = $operator;
            else throw new SqlException("SQLObject: Tried using an unknown operator '$operator'");
        }
       
        //Return a condtion for this sql object with the set boolean operator and '=' or 'like'
        public function whereCondition() {
            $condition = "";
            
            $sColumns = $this->getSearchColumns();
            $sValues = $this->getSearchValues();
            if (!Query::sameNumberOfColumnsAndValues($sColumns, $sValues)) throw new \Exception("Column/Value set length mismatch");
            else {
                list("columns" => $columns, "values" => $values) = Query::getUseableColumnsAndValues($sColumns, $sValues);
                //For each column
                $numColumns = count($columns);
                for ($i = 0;$i < $numColumns;$i++) {
                    //Set column = value sql string
                    $condition .= Query::getColumnEqualsValueSql($columns[$i], $values[$i], $this->equalityOperator);
                    if ($i < $numColumns-1) //If we are not on the last condition, add the operator
                        $condition .= Query::getLogicalOperatorSql($this->booleanConditon);
                }
            }
            var_dump($condition);
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

        //Get the tablename used by this object
        public function getTableName() { return $this->tableName; }
        //Set the tablename used by this object
        public function setTableName($tableName) { 
            $this->tableName = $tableName; 
            $this->table = new Table($tableName);
        }
    }
}

?>
