<?php

namespace MySqlConnector {
    class InsertGroup extends ExceptionThrower implements IParameterizedItem {
        private 
            $columns = array(),
            $values = array();
        
        public function __construct($columns, $values) {
            self::throwIfMismatch($columns, $values);
            $this->columns = $columns;
            $this->values = $values;
        }

        public static function throwIfMismatch($columns, $values) {
            if (count($columns) != count($values))
                self::throw("Must provide same number of columns and values.");
        }

        public function getQueryString($withValues = false) {
            return QueryBuilder::buildPreparableList(count($this->columns));
        }

        public function getOrderedParameters() {
            return $this->values;
        }

        public function getOrderedTypes() {
            return Condition::getValueTypes($this->values);
        }

        public function hasAnyParameters() {
            return count($this->values) > 0;
        }

        public function add($column, $value) {
            array_push($this->columns, $column);
            array_push($this->values, $value);
        }
    }
}

?>