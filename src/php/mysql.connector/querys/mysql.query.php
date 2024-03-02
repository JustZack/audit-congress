<?php

namespace MySqlConnector {

    class Query {
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
            if ($result->failure()) throw new \Exception(Connection::lastError());
            return $result;   
        }

        //Run this query
        public function execute() {
            $connection = Connection::getConnection();
            $result = new Result($connection->query($this->sql_formated), $this->sql_formated);
            return Query::throwIfError($result);
        }
        //Run many queries that have already been appended
        public function executeMany() {
            $connection = Connection::getConnection();
            $result = new Result($connection->multi_query($this->sql_formated), $this->sql_formated);
            return Query::throwIfError($result);
        }



        //For running queries that should return a result
        public static function getResult($sql, $params = null) {
            $query = new Query($sql, $params);
            return $query->execute();
        }
        //For running  returing true or false (success values)
        public static function runActionQuery($sql, $params = null) {
            return Query::getResult($sql, $params)->success();
        }
        //For running queries that return rows
        public static function runQuery($sql, $params = null) {
            return Query::getResult($sql, $params)->fetchAll();
        }
        //Build a formattable list with $numItems, like '(%s, %s, %s...)'
        public static function buildFormattableList($numItems, $withParens = true, $quoteChar = "'") {
            $itemFormat = "$quoteChar%s$quoteChar";
            $sql = $withParens ? "(" : "";
            for ($i = 0;$i < $numItems;$i++) $sql .= $i < $numItems - 1 ? "$itemFormat, " : $itemFormat;
            $sql .= $withParens ? ")" : "";
            return $sql;
        }

        //Build a list with the given items, parenthesis, and quote character
        public static function buildList($items, $withParens = true, $quoteChar = "'") {
            $listFormat = Query::buildFormattableList(count($items), $withParens, $quoteChar);
            $cleanItems = array();
            $conn = Connection::getConnection();
            foreach ($items as $item) array_push($cleanItems, $conn->real_escape_string($item));
            $sql = sprintf($listFormat, ...$cleanItems);
            return $sql;
        }
    }
}

?>