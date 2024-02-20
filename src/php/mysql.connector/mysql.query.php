<?php

namespace MySqlConnector {
    class Query {
        public 
            $params = array(), 
            $sql_formated = "";
        public function __construct($sql_string = null, $params = null) {
            if ($sql_string != null) $this->appendQuery($sql_string, $params);
        }

        //Append a query to this query
        public function appendQuery($sql_string, $params = null) {
            //Always put a semicolon at the end of a query
            $sql = $sql_string.";";
            if ($params != null) {
                $this->sql_formated .= sprintf($sql, ...$params);
                array_merge($this->params, $params);
            } else $this->sql_formated .= $sql;
        }
        //Tell the connection to use the given database
        public function useDatabase($database) {
            Connection::useDatabase($database);
        }

        //Run this query
        public function execute() {
            $connection = Connection::getConnection();
            return new Result($connection->query($this->sql_formated));   
        }
        //Run many queries that have already been appended
        public function executeMany() {
            $connection = Connection::getConnection();
            return new Result($connection->multi_query($this->sql_formated));   
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
            $sql = sprintf($listFormat, ...$items);
            return $sql;
        }
    }
}

?>