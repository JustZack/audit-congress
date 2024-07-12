<?php

namespace MySqlConnector {
    class WhereClause extends ConditionGroupUser {
        public function __construct(ConditionGroup $onConditions = null) {
            parent::__construct($onConditions);
        }

        public function getQueryString($withValues = false) {
            return $this->buildQueryString($withValues, "WHERE %s");
        }
    }
}

?>