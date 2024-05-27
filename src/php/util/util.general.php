<?php

namespace Util {
    class General {
        public static function allNull(...$vars) {
            foreach ($vars as $var) 
                if (!is_null($var)) 
                    return false;
            return true;
        }
    }

    trait GetInstance {
        private static $theInstance = null;
        public static function getInstance() {
            if (self::$theInstance == null) 
                self::$theInstance = new static();
            return self::$theInstance;
        }
    }
}

?>