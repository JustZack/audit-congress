<?php

namespace MySqlConnector {
    class Condition extends ExceptionThrower implements ConditionItem {
        private 
            $column,
            $operator,
            $value,
            $valueIsColumn;
        public function __construct($column, $operator, $value, $valueIsColumnName = false) {
            self::throwIfInvalidOperator($operator);
            self::throwIfOperatorDoesntMatchValue($operator, $value);

            $this->column = $column;
            $this->operator = $operator;
            $this->value = $value;
            $this->valueIsColumn = $valueIsColumnName;
        }

        public static function throwIfInvalidOperator($op) {
            if (!ComparisonOperators::isOne($op))
                self::throw("$op is not known to \MySqlConnector::ComparisonOperators.");
        }

        private static function throwIfOperatorDoesntMatchValue($op, $val) {
            $valIsArray = is_array($val);
            $opIsBetween = $op == ComparisonOperators::BETWEEN;
            $opIsIn = $op == ComparisonOperators::IN;
            if ($opIsBetween && (!$valIsArray || ($valIsArray && count($val) != 2))) {
                self::throw("$op expects exactly two values. Found $val");
            } else if ($opIsIn && !$valIsArray) {
                self::throw("$op operator requires array(n) of values. Found $val");
            } else if ($valIsArray && !$opIsBetween && !$opIsIn) {
                self::throw("$op operator requires a single value. Found $val");
            }
        }

        public function getQueryString($withValues = false) {
            $sql = sprintf("%s %s %s", $this->column, $this->operator, "%s");
            $valueString = "";
            if ($this->operator == ComparisonOperators::BETWEEN) {
                $valueString = $this->betweenValueConditionString();
            } else if ($this->operator == ComparisonOperators::IN) {
                $valueString = $this->inValueConditionString();
            } else {
                $valueString = $this->singleValueConditionString();
            }
            return sprintf($sql, $valueString);
        }

        private function betweenValueConditionString() {
            $sql = "%s AND %s";
            if ($this->valueIsColumn)
                return sprintf($sql, $this->value[0], $this->value[1]);
            else 
                return sprintf($sql, "?", "?");                    
        }

        private function inValueConditionString() {
            $sql = "%s";
            if ($this->valueIsColumn)
                return sprintf($sql, QueryBuilder::buildItemList($this->value));
            else 
                return sprintf($sql, QueryBuilder::buildPreparableList(count($this->value)));
        }

        private function singleValueConditionString() {
            $sql = "%s";
            if ($this->valueIsColumn)
                return sprintf($sql, "`".$this->value."`");
            else 
                return sprintf($sql, "?");
        }

        public function getOrderedParameters() {
            if ($this->valueIsColumn) return [];
            else return $this->value;
        }
    }
}

?>