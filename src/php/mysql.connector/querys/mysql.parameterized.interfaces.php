<?php

namespace MySqlConnector {
    interface IInvalidOperatorThrower {
        public static function throwIfInvalidOperator($op);
    }
    interface IParameterizedItem {
        public function getQueryString($withValues = false);
        public function getOrderedParameters();
        public function getOrderedTypes();
    }
    interface IConditionGroup extends IParameterizedItem {
        public function addCondition(Condition $c, $logicalOperator = null);
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null);
    }
    abstract class ConditionGroupUser implements IConditionGroup {
        protected ConditionGroup $group;

        public function __construct(ConditionGroup $group = null) {
            if ($group == null) $this->group = new ConditionGroup();
            else $this->group = $group;
        }

        public function addCondition(Condition $c, $logicalOperator = null) {
            $this->group->addCondition($c, $logicalOperator);
            return $this;
        }
        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null) {
            $this->group->addConditionGroup($cg, $logicalOperator);
            return $this;
        }

        public abstract function getQueryString($withValues = false);
        public function getOrderedParameters() {
            return $this->group->getOrderedParameters();
        }
        public function getOrderedTypes() {
            return $this->group->getOrderedTypes();
        }
        public function hasAnyConditions() {
            return $this->group->hasAny();
        }
    }
}

?>