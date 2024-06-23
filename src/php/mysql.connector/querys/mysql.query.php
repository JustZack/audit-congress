<?php

namespace MySqlConnector {

    class Query extends ExceptionThrower {
        public static $totalQueries = 0;
        public 
            $params = array(), 
            $sql_formated = "";
        public function __construct($sql_string = null, $params = null) {
            if ($sql_string != null) $this->appendQuery($sql_string, $params);
        }

        //Get a Show Tables query with the where condition
        public static function showTables($whereCondition = null) {
            $sql = "SHOW TABLES";
            if ($whereCondition != null) $sql .= " WHERE $whereCondition";
            return new Query($sql);
        }

        //Get a Describe query with the table name
        public static function describe($tableName) {
            return new Query("DESCRIBE $tableName");
        }

        //Append a query to this query
        public function appendQuery($sql_string, $params = null) {
            //Always put a semicolon at the end of a query
            $sql = $sql_string.";\n";
            if ($params != null) {
                $this->sql_formated .= sprintf($sql, ...$params);
                array_merge($this->params, $params);
            } else $this->sql_formated .= $sql;
        }
        //Tell the connection to use the given database
        public function useDatabase($database) {
            Connection::useDatabase($database);
        }

        //Throw the SQL error if the result failed
        private static function throwIfError($result) {
            if ($result->failure()) self::throw(Connection::lastError());
            return $result;   
        }

        //Run this query
        public function execute() {
            self::$totalQueries += 1;
            $connection = Connection::getConnection();
            $result = new Result($connection->query($this->sql_formated), $this->sql_formated);
            return self::throwIfError($result);
        }
        //Run many queries that have already been appended
        public function executeMany() {
            self::$totalQueries += 1;
            $connection = Connection::getConnection();
            $result = new Result($connection->multi_query($this->sql_formated), $this->sql_formated);
            return self::throwIfError($result);
        }



        //For running queries that should return a result
        public static function getResult($sql, $params = null) {
            $query = new Query($sql, $params);
            return $query->execute();
        }
        //For running  returing true or false (success values)
        public static function runActionQuery($sql, $params = null) {
            return self::getResult($sql, $params)->success();
        }
        //For running queries that return rows
        public static function runQuery($sql, $params = null) {
            return self::getResult($sql, $params)->fetchAll();
        }
    }
}

?>