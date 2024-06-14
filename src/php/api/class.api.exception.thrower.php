<?php

namespace API {
    class ExceptionThrower {
        //throw an API exception, where $from is like \API\ExceptionThrower\$function: $message
        public static function throwException($function, $message) {
            $className = static::class;
            throw new \API\Exception("$className\\$function: $message");
        }
    }
}

?>