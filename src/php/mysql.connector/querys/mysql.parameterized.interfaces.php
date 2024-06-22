<?php

namespace MySqlConnector {
    interface IInvalidOperatorThrower {
        public static function throwIfInvalidOperator($op);
    }
    interface IParameterizedItem {
        public function getQueryString($withValues = false);
        public function getOrderedParameters();
    }
    interface IConditionGroup extends IParameterizedItem {
        public function addCondition(Condition $c, $logicalOperator = null);
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null);
    }
    abstract class ConditionGroupUser implements IConditionGroup {
        protected ?ConditionGroup $group = null;
        public function addCondition(Condition $c, $logicalOperator = null) {
            $this->group->addCondition($c, $logicalOperator);
        }
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null) {
            $this->group->addConditionGroup($cg, $logicalOperator);
        }
        public abstract function getQueryString($withValues = false);
        public function getOrderedParameters() {
            return $this->group->getOrderedParameters();
        }
    }


}

?>