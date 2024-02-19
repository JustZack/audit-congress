<?php

namespace MySqlConnector {

    use Exception;

    class Table {
        private 
            $tableExists = null,
            $tableColumns = null;
        private ?Columns $columns = null;
        public $name;
        

        public function __construct($tableName) {
            $this->name = $tableName;
        }

        public function runActionQuery($sql, $params = null) {
            $result = (new Query($sql, $params))->execute();
            return $result->success();
        }

        public function runQuery($sql, $params = null) {
            $query = new Query($sql, $params);
            return $query->execute()->fetchAll();
        }

        //Check if this table exists
        public function exists($useCache = true) {
            $sql = "SHOW TABLES LIKE '$this->name'";
            if ($this->tableExists == null || !$useCache) {
                $results = $this->runQuery($sql);
                $this->tableExists = count($results) == 1;
            }
            return $this->tableExists;
        }
        //Describe the columns in this table
        public function columns() {
            $sql = "DESCRIBE `$this->name`";
            if ($this->tableColumns == null) {
                $results = $this->runQuery($sql);
                $this->columns = new Columns($results);
            }
            return $this->columns;
        }
        //Count the number of rows in this table
        public function count($whereCondition = null) {
            $sql = "SELECT COUNT(*) FROM `$this->name`";
            if ($whereCondition != null) { 
                $sql .= " WHERE %s";
                $results = $this->runQuery($sql, [$whereCondition]);
            } else {
                $results = $this->runQuery($sql);
            }
            return (int)$results[0][0];
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


        public function insert($columns, $values) {
            $sql = "INSERT INTO `$this->name` %s VALUES %s";

            $numCols = count($columns); $numValues = count($values);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name INSERT: Column count ($numCols) doesnt match value count ($numValues)");
            
            $itemList = Query::buildItemList($numCols);
            $sql = sprintf($sql, $itemList, $itemList);
            
            $colsAndValues = array_merge($columns, $values);

            return $this->runActionQuery($sql, $colsAndValues);
        }

        public function insertObject(SqlObject $sqlObj) {
            return $this->insert($sqlObj->getColumns(), $sqlObj->getValues());
        }

        public function update($columns, $values, $whereCondition) {
            //UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition;
            $sql = "UPDATE `$this->name` SET %s WHERE %s";

            $numCols = count($columns); $numValues = count($values);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name UPDATE: Column count ($numCols) doesnt match value count ($numValues)");

            $colValuesAndWhere = array();
            for ($i = 0;$i < $numCols;$i++) array_push($colValuesAndWhere, $columns[$i]." = ".$values[$i]);

            $itemList = Query::buildItemList($numCols, false);
            $sql = sprintf($sql, $itemList, "%s");

            array_push($colValuesAndWhere, $whereCondition);
            
            return $this->runActionQuery($sql, $colValuesAndWhere);
        }

        public function updateObject(SqlObject $sqlObj, $whereCondition) {
            return $this->update($sqlObj->getColumns(), $sqlObj->getValues(), $whereCondition);
        }

        public function delete($whereCondition) {
            $sql = "DELETE FROM `$this->name` WHERE %s";
            return $this->runActionQuery($sql, [$whereCondition]);
        }

        //Where $type is one of: ADD, DROP, MODIFY
        public function alter($type, $columnName, $columnDescription = null) {
            $sql = "ALTER TABLE `$this->name`";
            switch ($type) {
                case "ADD":    $sql .= " ADD $columnName $columnDescription"; break;
                case "DROP":   $sql .= " DROP COLUMN $columnName"; break;
                case "MODIFY": $sql .= " MODIFY COLUMN $columnName $columnDescription"; break;
                default: throw new \Exception("Unknown alter type '$type' for table $this->name. Use ADD, DROP, or MODIFY.");
            }
            $this->tableColumns == null;
            return $this->runActionQuery($sql);
        }

        public function addColumn($columnName, $columnDescription) { return $this->alter("ADD", $columnName, $columnDescription); }
        public function dropColumn($columnName) { return $this->alter("DROP", $columnName); }
        public function modifyColumn($columnName, $columnDescription) { return $this->alter("MODIFY", $columnName, $columnDescription); }
    } 
}

?>