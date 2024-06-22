<?php

namespace MySqlConnector {
    class JoinClause {
        private $table = null;
        private ?ConditionGroup $onConditions = null;
        
        public function __construct($table, ConditionGroup $onConditions = null) {
            
        }
    }
}

?>