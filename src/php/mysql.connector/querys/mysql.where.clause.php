<?php

namespace MySqlConnector {
    class WhereClause extends ConditionGroupUser {
        public function __construct(ConditionGroup $onConditions = null) {
            parent::__construct($onConditions);
        }

        public function getQueryString($withValues = false) {
            $sql = "";
            if ($this->hasAnyConditions()) {
                $sql = "WHERE %s";
                $sql = sprintf($sql, $this->group->getQueryString());
            }
            return $sql;
        }
    }
}

?>