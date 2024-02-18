<?php

namespace MySqlConnector {
    class Table {
        private 
            $tableExists = null,
            $tableColumns = null,
            $rowCount = null;
        private ?Columns $columns = null;
        public $name;
        

        public function __construct($tableName) {
            $this->name = $tableName;
        }

        public function exists() {
            if ($this->tableExists == null) {
                $query = new Query("show tables like '$this->name'");
                $results = $query->execute()->fetchAll();
                $this->tableExists = count($results) == 1;
            }
            return $this->tableExists;
        }

        public function columns() {
            if ($this->tableColumns == null) {
                $query = new Query("describe $this->name");
                $results = $query->execute()->fetchAll();
                $this->columns = new Columns($results);
            }
            return $this->columns->items();
        }

        public function count($useCache = true) {
            if ($this->rowCount == null or !$useCache) {
                $query = new Query("select count(*) from $this->name");
                $results = $query->execute()->fetchAll();
                $this->rowCount = (int)$results[0][0];
            }
            return $this->rowCount;
        }

        public function runActionQuery($sql, $params = null) {
            $result = (new Query($sql, $params))->execute();
            return $result->success();
        }

        public function create($sql_column_descriptions_array) {
            $sql = "CREATE TABLE `$this->name` %s";
            $sql = sprintf($sql, Query::buildItemList(count($sql_column_descriptions_array)));
            return $this->runActionQuery($sql, $sql_column_descriptions_array);
        }

        public function drop() {
            $sql = "DROP TABLE `$this->name`";
            return $this->runActionQuery($sql);
        }

        public function insert($columnOrder, $columnValues) {
            $sql = "INSERT INTO `$this->name` %s VALUES %s";

            $numCols = count($columnOrder); $numValues = count($columnValues);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name INSERT: Column count ($numCols) doesnt match value count ($numValues)");
            
            $itemList = Query::buildItemList($numCols);
            $sql = sprintf($sql, $itemList, $itemList);
            
            $colsAndValues = array_merge($columnOrder, $columnValues);

            return $this->runActionQuery($sql, $colsAndValues);
        }

        public function update($columnOrder, $columnValues, $whereCondition) {
            //UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition;
            $sql = "UPDATE $this->name SET %s WHERE %s";

            $numCols = count($columnOrder); $numValues = count($columnValues);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name UPDATE: Column count ($numCols) doesnt match value count ($numValues)");

            $colValuesAndWhere = array();
            for ($i = 0;$i < $numCols;$i++) array_push($colValuesAndWhere, $columnOrder[$i]." = ".$columnValues[$i]);

            $itemList = Query::buildItemList($numCols, false);
            $sql = sprintf($sql, $itemList, "%s");

            array_push($colValuesAndWhere, $whereCondition);
            
            return $this->runActionQuery($sql, $colValuesAndWhere);
        }

        public function addColumn($columnName, $columnDescription) {
            $sql = "ALTER TABLE `$this->name` add $columnName $columnDescription";
            return $this->runActionQuery($sql);
        }

        public function dropColumn($columnName) {
            $sql = "ALTER TABLE `$this->name` DROP COLUMN $columnName";
            return $this->runActionQuery($sql);
        }

        public function modifyColumn($columnName, $columnDescription) {
            $sql = "ALTER TABLE `$this->name` MODIFY COLUMN $columnName $columnDescription";
            return $this->runActionQuery($sql);
        }
    } 
}

?>