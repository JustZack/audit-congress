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
}

?>