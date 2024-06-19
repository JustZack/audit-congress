<?php

namespace MySqlConnector {
    class Condition extends ExceptionThrower implements ConditionItem {
        private 
            $column,
            $operator,
            $value;
        public function __construct($column, $operator, $value) {
            self::throwIfInvalidOperator($operator);
            self::throwIfOperatorDoesntMatchValue($operator, $value);

            $this->column = $column;
            $this->operator = $operator;
            $this->value = $value;
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

        public function getParameterizedString() {
            $sql = sprintf("%s %s %s", $this->column, $this->operator, "%s");
            if ($this->operator == ComparisonOperators::BETWEEN) {
                $sql = sprintf($sql, "? AND ?");
            } else if ($this->operator == ComparisonOperators::IN) {
                $sql = sprintf($sql, QueryBuilder::buildPreparableList(count($this->value)));
            } else $sql = sprintf($sql, "?");
            return $sql;
        }

        public function getOrderedParameters() {
            return $this->value;
        }
    }
}

?>