<?php

namespace MySqlConnector {
    interface ConditionItem {
        public function getParameterizedString();
        public function getOrderedParameters();
        public static function throwIfInvalidOperator($op);
    }
}

?>