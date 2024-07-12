<?php

namespace MySqlConnector {
    abstract class Operators {
        public static abstract function operators();
        public static function isOne($operator) {
            return in_array(strtoupper($operator), static::operators());
        }
    }    
    abstract class Comparison extends Operators {
        public const
            LIKE = "LIKE",
            BETWEEN = "BETWEEN",
            IN = "IN",
            EQUALS = "=",
            NOT_EQUALS = "<>",
            LESS_THAN_EQUALS = "<=",
            GREATER_THAN_EQUALS = ">=",
            LESS_THAN = "<",
            GREATER_THAN = ">";
        public static function operators() {
            return [self::LIKE, self::BETWEEN, self::IN, self::EQUALS, self::NOT_EQUALS, 
            self::LESS_THAN_EQUALS, self::GREATER_THAN_EQUALS, self::LESS_THAN, self::GREATER_THAN];
        }
    }

    abstract class Logical extends Operators {
        const
            AND = "AND",
            OR = "OR",
            XOR = "XOR";
        public static function operators() {
            return [self::AND, self::OR, self::XOR];
        }
    }
}

?>