<?php

namespace MySqlConnector {
    class ConditionGroup extends ExceptionThrower implements ConditionItem {
        private 
            $conditions = array(),
            $operators = array(),
            $defaultOperator = "";
        
        public function __construct($defaultOperator = LogicalOperators::AND) {
            $this->throwIfInvalidOperator($defaultOperator);
            $this->defaultOperator = $defaultOperator;
        }

        public static function throwIfInvalidOperator($op) {
            if (!LogicalOperators::isOne($op))
                self::throw("$op is not known to \MySqlConnector::LogicalOperators.");
        }

        public function getQueryString($withValues = false) {
            $sql = "(";
            for ($i = 0;$i < count($this->conditions);$i++) {
                $condition = $this->conditions[$i];
                $sql .= $condition->getQueryString($withValues);
                //If this isn't the last condition, an operator comes next
                if ($i < count($this->conditions)-1) {
                    $operator = $this->operators[$i];
                    $sql .= " $operator ";
                }
            }
            $sql .= ")";
            return $sql;
        }

        public function getOrderedParameters() {
            $parameters = array();
            for ($i = 0;$i < count($this->conditions);$i++) {
                $condition = $this->conditions[$i];
                $value = $condition->getOrderedParameters();
                if (is_array($value)) $parameters = array_merge($parameters, $value);
                else array_push($parameters, $value);
            }
            return $parameters;
        }

        public function addCondition(Condition $c, $logicalOperator = null) {
            $this->addItem($c, $logicalOperator);

        }

        public function addConditionGroup(ConditionGroup $cg, $logicalOperator = null) {
            $this->addItem($cg, $logicalOperator);
        }
        
        private function addItem($con, $logicalOperator = null) {
            //Only need to add a logical after the first condition has been added.
            if (count($this->conditions) > 0) {
                $op = $logicalOperator != null ? $logicalOperator : $this->defaultOperator;
                self::throwIfInvalidOperator($op);
                array_push($this->operators, $op);
            }
            array_push($this->conditions, $con);
        }
    }
}

?>