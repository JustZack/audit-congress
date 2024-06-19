<?php

namespace MySqlConnector {
    interface ConditionItem extends ParameterizedItem{
        public static function throwIfInvalidOperator($op);
    }

    interface ParameterizedItem {
        public function getParameterizedString();
        public function getOrderedParameters();
    }
}

?>