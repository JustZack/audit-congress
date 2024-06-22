<?php

namespace MySqlConnector {

    abstract class ConditionGroupUser implements IConditionGroup {
        protected ?ConditionGroup $group = null;
        public function addCondition(Condition $c, $logicalOperator = null) {
            $this->group->addCondition($c, $logicalOperator);
        }
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null) {
            $this->group->addConditionGroup($cg, $logicalOperator);
        }
    }

    interface IConditionGroup {
        public function addCondition(Condition $c, $logicalOperator = null);
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null);
    }
    interface IInvalidOperatorThrower {
        public static function throwIfInvalidOperator($op);
    }
    interface IParameterizedItem {
        public function getQueryString($withValues = false);
        public function getOrderedParameters();
    }
}

?>