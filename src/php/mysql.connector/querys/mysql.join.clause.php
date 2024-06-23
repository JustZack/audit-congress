<?php

namespace MySqlConnector {
    class JoinClause extends ConditionGroupUser {
        private $table = null;
        public function __construct($table, ConditionGroup $onConditions = null) {
            $this->table = $table;
            parent::__construct($onConditions);
        }

        public function getQueryString($withValues = false) {
            $sql = "";
            if ($this->hasAnyConditions()) {
                $sql = "JOIN %s ON %s";
                $sql = sprintf($sql, $this->table, $this->group->getQueryString());
            }
            return $sql;

        }
    }
}

?>