<?php

namespace MySqlConnector {
    class InsertGroup extends ExceptionThrower implements IParameterizedItem {
        protected 
            $columns = array(),
            $values = array();
        
        public function __construct(SqlRow $row) {
            $this->columns = $row->getColumns();
            $this->values = $row->getValues();
            self::throwIfMismatch($this->columns, $this->values);
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

        public function columns() { return $this->columns; }

        public function add($column, $value) {
            array_push($this->columns, $column);
            array_push($this->values, $value);
        }
    }
}

?>