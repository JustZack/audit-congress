<?php

namespace MySqlConnector {
    class JoinClause extends ConditionGroupUser implements IParameterizedItem {
        private $table = null;
        
        public function __construct($table, ConditionGroup $onConditions = null) {
            $this->table = $table;
            if ($onConditions != null) $this->group = $onConditions;
        }

        public function getQueryString($withValues = false) {
            $sql = "JOIN %s ON %s";
            return sprintf($sql, $this->table, $this->group->getQueryString());
        }
        public function getOrderedParameters() {
            return $this->group->getOrderedParameters();
        }
    }
}

?>