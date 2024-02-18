<?php

namespace MySqlConnector {
    class Query {
        public 
            $sql, 
            $params, 
            $sql_formated;
        public function __construct($sql_string, $params = null) {
            $this->sql = $sql_string;
            if ($params != null) {
                $this->params = $params;
                $this->sql_formated = sprintf($this->sql, ...$this->params);
            } else $this->sql_formated = $this->sql;
        }

        public function useDatabase($database) {
            $connection = Connection::useDatabase($database);
        }

        public function execute() {
            $connection = Connection::getConnection();
            return new Result($connection->query($this->sql_formated));   
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