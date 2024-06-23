<?php

namespace MySqlConnector {
    class Condition extends ExceptionThrower implements IInvalidOperatorThrower, IParameterizedItem {
        private 
            $column,
            $operator,
            $value,
            $type,
            $valueIsColumn;

        public function __construct($column, $operator, $value, $valueIsColumnName = false) {
            self::throwIfInvalidOperator($operator);
            self::throwIfOperatorDoesntMatchValue($operator, $value);

            $this->column = $column;
            $this->operator = $operator;
            $this->value = $value;
            $this->type = self::getValueTypes($this->value);
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

        public static function getValueTypes($value) {
            $typeStr = "";
            if (is_array($value)) foreach ($value as $val) $typeStr .= self::getValueType($val);
            else $typeStr = self::getValueType($value);
            return $typeStr;
        }
        public static function getValueType($value) {
            if (is_string($value)) return "s";
            else if (is_float($value)) return "f";
            else if (is_int($value)) return "i";
            else self::throw("Unrecognized Type for Value: `$value`. Expected string, float, or int.");
        }
        public function getOrderedTypes() {
            return $this->type;
        }
    }
}

?>