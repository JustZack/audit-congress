<?php

namespace MySqlConnector {
    class Condition extends ExceptionThrower implements IInvalidOperatorThrower, IParameterizedItem {
        private 
            $column,
            $operator,
            $value,
            $type,
            $notBindable;

        public function __construct($column, $operator, $value, $notBindable = false) {
            self::throwIfInvalidOperator($operator);
            self::throwIfOperatorDoesntMatchValue($operator, $value);

            $this->column = $column;
            $this->operator = $operator;
            $this->value = $value;
            $this->type = self::getValueTypes($this->value);
            $this->notBindable = $notBindable;
        }

        public static function throwIfInvalidOperator($op) {
            if (!Comparison::isOne($op))
                self::throw("$op is not known to \MySqlConnector::Comparison.");
        }

        private static function throwIfOperatorDoesntMatchValue($op, $val) {
            $valIsArray = is_array($val);
            $opIsBetween = $op == Comparison::BETWEEN;
            $opIsIn = $op == Comparison::IN;
            if ($opIsBetween && (!$valIsArray || ($valIsArray && count($val) != 2))) {
                self::throw("$op expects exactly two values. Found $val");
            } else if ($opIsIn && !$valIsArray) {
                self::throw("$op operator requires array(n) of values. Found $val");
            } else if ($valIsArray && !$opIsBetween && !$opIsIn) {
                self::throw("$op operator requires a single value. Found $val");
            }
        }

        public function getQueryString($withValues = false) {
            $colPart = $this->column;
            if (!$this->notBindable) $colPart = "`$colPart`";
            $sql = sprintf("%s %s %s", $colPart, $this->operator, "%s");

            $valueString = "";
            if ($this->operator == Comparison::BETWEEN) {
                $valueString = $this->betweenValueConditionString();
            } else if ($this->operator == Comparison::IN) {
                $valueString = $this->inValueConditionString();
            } else {
                $valueString = $this->singleValueConditionString();
            }
            return sprintf($sql, $valueString);
        }

        private function betweenValueConditionString() {
            $sql = "%s AND %s";
            if ($this->notBindable)
                return sprintf($sql, $this->value[0], $this->value[1]);
            else 
                return sprintf($sql, "?", "?");                    
        }

        private function inValueConditionString() {
            $sql = "%s";
            if ($this->notBindable)
                return sprintf($sql, QueryBuilder::buildItemList($this->value));
            else 
                return sprintf($sql, QueryBuilder::buildPreparableList(count($this->value)));
        }

        private function singleValueConditionString() {
            $sql = "%s";
            if ($this->notBindable)
                return sprintf($sql, $this->value);
            else 
                return sprintf($sql, "?");
        }

        public function getOrderedParameters() {
            if ($this->notBindable) return [];
            else return $this->value;
        }

        public static function getValueTypes($value) {
            $typeStr = "";
            if (is_array($value)) foreach ($value as $val) $typeStr .= self::getValueType($val);
            else $typeStr = self::getValueType($value);
            return $typeStr;
        }
        public static function getValueType($value) {
            if (is_string($value) || is_null($value)) return "s";
            else if (is_float($value)) return "f";
            else if (is_int($value) || is_bool($value)) return "i";
            else self::throw("Unrecognized Type for Value: `$value`. Expected string, float, or int.");
        }
        public function getOrderedTypes() {
            if ($this->notBindable) return "";
            else return $this->type;
        }

        public function hasAnyParameters() {
            return !$this->notBindable;
        }
    }
}

?>