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

        public function appendQuery($sql_string, $params = null) {
            //Always put a semicolon at the end of a query
            $sql = $sql_string.";";
            if ($params != null) {
                $this->sql_formated .= sprintf($sql, ...$params);
            } else $this->sql_formated .= $sql;
        }

        public function useDatabase($database) {
            $connection = Connection::useDatabase($database);
        }

        public function execute() {
            $connection = Connection::getConnection();
            return new Result($connection->query($this->sql_formated));   
        }

        public function executeMany() {
            $connection = Connection::getConnection();
            return new Result($connection->multi_query($this->sql_formated));   
        }

        public static function buildItemList($numItems, $withParens = true) {
            $sql = $withParens ? "(" : "";
            for ($i = 0;$i < $numItems;$i++) $sql .= $i < $numItems - 1 ? "%s, " : "%s";
            $sql .= $withParens ? ")" : "";
            return $sql;
        }
    }
}

?>