<?php

namespace MySqlConnector {

    class Query extends ExceptionThrower {
        public static $totalQueries = 0;
        public 
            $sql = "",
            $params = null,
            $types = null;
        public function __construct($sql_string = null, $params = null, $types = null) {
            $this->sql = $sql_string;
            $this->params = $params;
            $this->types = $types;
            $this->parseQuery();  
        }

        public function isPreparable() {
            return $this->params != null && $this->types != null 
                && count($this->params) == strlen($this->types);
        }

        //Format the query with the given parameters (but only if no types are available)
        private function parseQuery() {
            if ($this->types != null && strlen($this->types) > 0) return;
            else if ($this->params != null && count($this->params) > 0) 
                $this->sql = sprintf($this->sql, ...$this->params);
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

        //Prepare this query into a prepared statement with params when applicable
        public function prepare() : \mysqli_stmt {
            $connection = Connection::getConnection();
            $statement = $connection->prepare($this->sql);

            if (!$statement) self::throw("Could not prepare sql: " . $this->sql);
            if ($this->isPreparable())
                if (!$statement->bind_param($this->types, ...$this->params))
                    self::throw("Error preparing statement: " . $statement->error);
            return $statement;
        }

        //Run this query
        public function execute() {
            self::$totalQueries += 1;
            $result = new Result($this);
            return self::throwIfError($result);
        }

        //For running queries that should return a result
        public static function getResult($sql, $params = null, $types = null) {
            $query = new Query($sql, $params, $types);
            return $query->execute();
        }
     
        //For running  returing true or false (success values)
        public static function runActionQuery($sql, $params = null, $types = null) {
            return self::getResult($sql, $params, $types)->success();
        }
        //For running queries that return rows
        public static function runQuery($sql, $params = null, $types = null) {
            return self::getResult($sql, $params, $types)->fetchAll();
        }
    }
}

?>