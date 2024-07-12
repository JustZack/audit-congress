<?php

namespace MySqlConnector {
    interface IInvalidOperatorThrower {
        public static function throwIfInvalidOperator($op);
    }
    interface IParameterizedItem {
        public function getQueryString($withValues = false);
        public function hasAnyParameters();
        public function getOrderedParameters();
        public function getOrderedTypes();
    }
    interface IConditionGroup extends IParameterizedItem {
        public function addCondition(Condition $c, $logicalOperator = null);
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null);
    }
}

?>