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

        public function execute() {
            $connection = Connection::getConnection();
            return new Result($connection->query($this->sql_formated));   
        }
    }
}

?>