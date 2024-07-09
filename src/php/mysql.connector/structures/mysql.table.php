<?php

namespace MySqlConnector {

    class Table extends ExceptionThrower {
        private 
            $tableExists = null,
            $tableColumns = null,
            $tableIndexes = null,
            $insertQueueSql = null,
            $insertParams = null,
            $insertTypes = null,
            $startingInsert = null;
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

        public function selectObject(QueryWrapper $query) {
            $o = $query;
            return $this->select($o->getSelectColumns(), $o->whereCondition(), 
            $o->getJoin(), $o->getGroupBy(), $o->getOrderBy(), $o->getLimit(), $o->getOffset());
        }


        //Insert a row with the provided $columns and $values
        public function insert(InsertGroup $insert) {
            $sql = $insert->asInsertStatement($this->name);
            return Query::runActionQuery($sql, $insert->getOrderedParameters(), $insert->getOrderedTypes());
        }
        //Queue an insert to be run
        public function queueInsert(InsertGroup $insert) {
            if ($this->insertQueueSql == null) {
                $this->startingInsert = $insert;
                $this->insertQueueSql = $insert->asInsertStatement($this->name);
                $this->insertParams = array();
                $this->insertTypes = "";
            } else if (!$this->startingInsert->sameColumnsAs($insert)) {
                self::throw("Insert Queue Failure: Must provide identical column names for each insert group.");
            } else {
                $this->insertQueueSql .= ",".$insert->getQueryString();
            }
            $this->insertParams = array_merge($this->insertParams, $insert->getOrderedParameters());
            $this->insertTypes .= $insert->getOrderedTypes();
        }
        //Commit queued inserts
        public function commitInsert() {
            if ($this->insertQueueSql == null) return false;
            $result = Query::runActionQuery($this->insertQueueSql, $this->insertParams, $this->insertTypes);

            $this->startingInsert = null;
            $this->insertQueueSql = null;
            $this->insertParams = null;
            $this->insertTypes = null;
            return $result;
        }
        
        //Update a row with the provided $columns and $values, where $whereCondition is satisfied 
        public function update(UpdateGroup $update, WhereClause $where) {
            //UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition;
            $sql = "UPDATE `$this->name` SET %s %s";
            $sql = sprintf($sql, $update->getQueryString(), $where->getQueryString());
            $values = array_merge($update->getOrderedParameters(), $where->getOrderedParameters());
            $types = $update->getOrderedTypes() . $where->getOrderedTypes();
            return Query::runActionQuery($sql, $values, $types);
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

        public function alterColumn($type, Column $column) {
            $sql = "ALTER TABLE `$this->name` ";
            switch ($type) {
                case AlterType::ADD:    
                    $sql .= "ADD %s %s"; 
                    $sql = sprintf($sql, $column->name(), $column->type());
                    break;
                case AlterType::DROP:
                    $sql .= "DROP COLUMN %s"; 
                    $sql = sprintf($sql, $column->name());
                    break;
                case AlterType::MODIFY:
                    $sql .= "MODIFY COLUMN %s %s"; 
                    $sql = sprintf($sql, $column->name(), $column->type());
                    break;
                default: self::throw("Unknown or unsupported column alter type '$type' for table $this->name. Use ADD, DROP, or MODIFY.");
            }
            $this->tableColumns == null;
            return Query::runActionQuery($sql);
        }

        public function alterIndex($type, Index $index) {
            $sql = "";
            switch ($type) {
                case AlterType::ADD:
                    $sql = "ALTER TABLE `$this->name` ADD INDEX %s %s";
                    $sql = sprintf($sql, $index->name(), $index->columns());
                    break;
                case AlterType::DROP:
                    $sql = "DROP INDEX %s ON `$this->name`"; 
                    $sql = sprintf($sql, $index->name());
                    break;
                case AlterType::MODIFY:
                    $this->alterIndex(AlterType::DROP, $index);
                    $this->alterIndex(AlterType::ADD, $index);
                    return;
                default: self::throw("Unknown or unsupported index alter type '$type' for table $this->name. Use ADD, DROP.");
            }
            $this->tableIndexes == null;
            return Query::runActionQuery($sql);
        }

        public function alter($structure, $type, $withObject) {
            switch ($structure) {
                case AlterStructure::COLUMN: $this->alterColumn($type, $withObject); break;
                case AlterStructure::INDEX: $this->alterIndex($type, $withObject); break;
                default: self::throw("Unknown or unsupported alter structure '$structure' for table $this->name. Use COLUMN or INDEX.");
            }
        }

    }

    abstract class AlterType {
        const ADD = "ADD";
        const DROP = "DROP";
        const MODIFY = "MODIFY";
    }

    abstract class AlterStructure {
        const INDEX = "INDEX";
        const COLUMN = "COLUMN";
    }
}

?>