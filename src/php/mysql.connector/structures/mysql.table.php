<?php

namespace MySqlConnector {

    class Table {
        private 
            $tableExists = null,
            $tableColumns = null,
            $tableIndexes = null;
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
        //List tables in the currently selection database
        public static function showTables() {
            return (new Database(Connection::getDatabase()))->showTables();
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
        //Show the indexes setup on this table
        public function indexes() {
            $sql = "SHOW INDEX FROM `$this->name`";
            if ($this->tableIndexes == null) {
                $results = Query::runQuery($sql);
                $this->tableIndexes = new Indexes($results);
            }
            return $this->tableIndexes;
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
            $list = QueryBuilder::buildItemList($sql_column_descriptions_array, true, "");
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
        public function select($selectColumns, $whereCondition = null, $join = null, $groupBy = null, $orderBy = null, $limit = null, $offset = null) : Result {
            $sql = "SELECT %s FROM `$this->name`";
            
            $colList = QueryBuilder::buildItemList($selectColumns, false, "");
            $sql = sprintf($sql, $colList);

            if ($join != null)           $sql .= sprintf(" JOIN %s", $join);
            if ($whereCondition != null) $sql .= sprintf(" WHERE %s", $whereCondition);
            if ($groupBy != null)        $sql .= sprintf(" GROUP BY %s", $groupBy);
            if ($orderBy != null)        $sql .= sprintf(" ORDER BY %s", $orderBy);
            if ($limit != null)          $sql .= sprintf(" LIMIT %s", $limit);
            if ($offset != null)         $sql .= sprintf(" OFFSET %s", $offset);
          
            return Query::getResult($sql);
        }

        public function selectObject(SqlObject $SQLObject) {
            $o = $SQLObject;
            return $this->select($o->getSelectColumns(), $o->whereCondition(), 
            $o->getJoin(), $o->getGroupBy(), $o->getOrderBy(), $o->getLimit(), $o->getOffset());
        }


        //Insert a row with the provided $columns and $values
        public function insert($columns, $values) {
            $sql = QueryBuilder::buildInsert($this->name, $columns, $values);
            
            return Query::runActionQuery($sql);
        }

        private $insertQueue = null;
        //Queue an insert to be run
        public function queueInsert($columns, $values) {
            if ($this->insertQueue == null)
                $this->insertQueue = QueryBuilder::buildInsert($this->name, $columns, $values);
            else 
                $this->insertQueue .= ",".QueryBuilder::buildItemList($values, true, "'");
        }
        //Commit queued inserts
        public function commitInsert() {
            if ($this->insertQueue == null) return false;
            $result = Query::runActionQuery($this->insertQueue);

            $this->insertQueue = null;
            return $result;
        }
        
        //Update a row with the provided $columns and $values, where $whereCondition is satisfied 
        public function update($columns, $values, $whereCondition) {
            //UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition;
            $sql = "UPDATE `$this->name` SET %s WHERE %s";

            $numCols = count($columns); $numValues = count($values);
            if ($numCols != $numValues) 
                throw new SqlException("$this->name UPDATE: Column count ($numCols) doesnt match value count ($numValues)");

            $colsAndValues = array();
            $values = QueryBuilder::escapeStrings($values);
            for ($i = 0;$i < $numCols;$i++) 
                array_push($colsAndValues, "`".$columns[$i]."` = '".$values[$i]."'");

            
            $colsAndValuesList = QueryBuilder::buildSetList($colsAndValues);

            $sql = sprintf($sql, $colsAndValuesList, $whereCondition);
            return Query::runActionQuery($sql);
        }
        //Delete a row where $whereCondition is satisfied
        public function delete($whereCondition) {
            $sql = "DELETE FROM `$this->name` WHERE %s";
            return Query::runActionQuery($sql, [$whereCondition]);
        }

        //Truncate (drop) all rows in this table
        public function truncate() {
            $sql = "TRUNCATE `$this->name`";
            return Query::runActionQuery($sql);
        }


        //Where $type is one of: ADD, DROP, MODIFY
        public function alter($type, $columnName, $columnDescription = null) {
            $sql = "ALTER TABLE `$this->name`";
            switch ($type) {
                case "ADD":    $sql .= " ADD $columnName $columnDescription"; break;
                case "DROP":   $sql .= " DROP COLUMN $columnName"; break;
                case "MODIFY": $sql .= " MODIFY COLUMN $columnName $columnDescription"; break;
                default: throw new SqlException("Unknown alter type '$type' for table $this->name. Use ADD, DROP, or MODIFY.");
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