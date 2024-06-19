<?php

namespace MySqlConnector {
    class ExceptionThrower {
        public static function throw($message) {
            $className = static::class;
            throw new SqlException("$className: $message");
        }
    }
}

?>