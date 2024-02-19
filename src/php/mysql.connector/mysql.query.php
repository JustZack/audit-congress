<?php

namespace MySqlConnector {
    class Query {
        public 
            $sql, 
            $params, 
            $sql_formated = "";
        public function __construct($sql_string = null, $params = null) {
            if ($params != null) $this->params = $params;

            if ($sql_string != null) {
                $this->sql = $sql_string;
                $this->appendQuery($this->sql, $this->params);
                //if ($this->params != null) {
                //    $this->sql_formated = sprintf($this->sql, ...$this->params);
                //} else $this->sql_formated = $this->sql;
            }
        }

        //Append a query to this query
        public function appendQuery($sql_string, $params = null) {
            //Always put a semicolon at the end of a query
            $sql = $sql_string.";";
            if ($params != null) {
                $this->sql_formated .= sprintf($sql, ...$params);
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



        //For running  returing true or false (success values)
        public static function runActionQuery($sql, $params = null) {
            $result = (new Query($sql, $params))->execute();
            return $result->success();
        }
        //For running queries that return rows
        public static function runQuery($sql, $params = null) {
            $query = new Query($sql, $params);
            return $query->execute()->fetchAll();
        }
        //Build a formattable list with $numItems, like '(%s, %s, %s...)'
        public static function buildItemList($numItems, $withParens = true, $quoteChar = "'") {
            $itemFormat = "$quoteChar%s$quoteChar";
            $sql = $withParens ? "(" : "";
            for ($i = 0;$i < $numItems;$i++) $sql .= $i < $numItems - 1 ? "$itemFormat, " : $itemFormat;
            $sql .= $withParens ? ")" : "";
            return $sql;
        }
    }
}

?>