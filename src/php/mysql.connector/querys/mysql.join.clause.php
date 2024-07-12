<?php

namespace MySqlConnector {
    class JoinClause extends ConditionGroupUser {
        private $table = null;
        public function __construct($table, ConditionGroup $onConditions = null) {
            $this->table = $table;
            parent::__construct($onConditions);
        }

        public function getQueryString($withValues = false) {
            return $this->buildQueryString($withValues, "JOIN `%s` ON %s", $this->table);
        }
    }
}

?>