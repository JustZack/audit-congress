<?php

namespace MySqlConnector {

    class Table {
        private 
            $tableExists = null,
            $tableColumns = null;
        private ?Columns $columns = null;
        public $name;
        


        public function __construct($tableName) {
            $this->name = $tableName;
        }



        //Check if this table exists
        public function exists($useCache = true) {
            $sql = "SHOW TABLES LIKE '$this->name'";
            if ($this->tableExists == null || !$useCache) {
                $results = Query::runQuery($sql);
                $this->tableExists = count($results) == 1;
            }
            return $this->tableExists;
        }
        //List tables in the currently selection database
        public static function listTables() {
            $sql = "SHOW TABLES";
            return Query::runQuery($sql);
        }
        //Change the database used by this connection
        public static function useDatabase($database) {
            Connection::useDatabase($database);
        }



        //Describe the columns in this table
        public function columns() {
            $sql = "DESCRIBE `$this->name`";
            if ($this->tableColumns == null) {
                $results = Query::runQuery($sql);
                $this->columns = new Columns($results);
            }
            return $this->columns;
        }
        //Count the number of rows in this table
        public function count($whereCondition = null) {
            $sql = "SELECT COUNT(*) FROM `$this->name`";
            if ($whereCondition != null) { 
                $sql .= " WHERE %s";
                $results = Query::runQuery($sql, [$whereCondition]);
            } else {
                $results = Query::runQuery($sql);
            }
            return (int)$results[0][0];
        }
        //Create this table with columns described by $sql_column_descriptions_array
        public function create($sql_column_descriptions_array) {
            $sql = "CREATE TABLE `$this->name` %s";
            $list = Query::buildList($sql_column_descriptions_array, true, "");
            $this->tableExists = null;
            return Query::runActionQuery($sql, [$list]);
        }
        //Drop this table
        public function drop() {
            $sql = "DROP TABLE `$this->name`";
            $this->tableExists = null;
            return Query::runActionQuery($sql);
        }



        //Select columns $selectColumns, where $whereCondition is satisfied, ordered by $orderBy
        public function select($selectColumns, $whereCondition = null, $orderBy = null) : Result {
            $sql = "SELECT %s FROM `$this->name`";
            
            $colList = Query::buildList($selectColumns, false, "");
            $sql = sprintf($sql, $colList);

            if ($whereCondition != null) $sql .= sprintf(" WHERE %s", $whereCondition);
            if ($orderBy != null)        $sql .= sprintf(" ORDER BY %s", $orderBy);
            
            return Query::getResult($sql);
        }
        //Insert a row with the provided $columns and $values
        public function insert($columns, $values) {
            $sql = "INSERT INTO `$this->name` %s VALUES %s";

            $numCols = count($columns); $numValues = count($values);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name INSERT: Column count ($numCols) doesnt match value count ($numValues)");
            
            $colList = Query::buildList($columns, true, "`");
            $valList = Query::buildList($values, true, "'");

            $sql = sprintf($sql, $colList, $valList);
            
            return Query::runActionQuery($sql);
        } 
        //Update a row with the provided $columns and $values, where $whereCondition is satisfied 
        public function update($columns, $values, $whereCondition) {
            //UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition;
            $sql = "UPDATE `$this->name` SET %s WHERE %s";

            $numCols = count($columns); $numValues = count($values);
            if ($numCols != $numValues) 
                throw new \Exception("$this->name UPDATE: Column count ($numCols) doesnt match value count ($numValues)");

            $colsAndValues = array();
            for ($i = 0;$i < $numCols;$i++) array_push($colsAndValues, "`".$columns[$i]."` = '".$values[$i]."'");

            
            $colsAndValuesList = Query::buildList($colsAndValues, false, "");

            $sql = sprintf($sql, $colsAndValuesList, $whereCondition);
            return Query::runActionQuery($sql);
        }
        //Delete a row where $whereCondition is satisfied
        public function delete($whereCondition) {
            $sql = "DELETE FROM `$this->name` WHERE %s";
            return Query::runActionQuery($sql, [$whereCondition]);
        }
        //Select an object based on the provided SQLObjects columns and whereCondition()
        public function selectObject(SqlObject $sqlObj) {
            $this->select($sqlObj->getColumns(), $sqlObj->whereCondition());
        }
        //Insert a row with provided SQLObjects columns and values
        public function insertObject(SqlObject $sqlObj) {
            return $this->insert($sqlObj->getColumns(), $sqlObj->getValues());
        }
        //Update a row with provided SQLObjects columns, values, and whereCondition()
        public function updateObject(SqlObject $sqlObj) {
            return $this->update($sqlObj->getColumns(), $sqlObj->getValues(), $sqlObj->whereCondition());
        }
        //Delete a row where the SQLObjects whereCondition is satisfied
        public function deleteObject(SqlObject $sqlObj) {
            return $this->delete($sqlObj->whereCondition());
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
            return Query::runActionQuery($sql);
        }
        //Alias's for the alter function
        public function addColumn($columnName, $columnDescription) { return $this->alter("ADD", $columnName, $columnDescription); }
        public function dropColumn($columnName) { return $this->alter("DROP", $columnName); }
        public function modifyColumn($columnName, $columnDescription) { return $this->alter("MODIFY", $columnName, $columnDescription); }
    } 
}

?>