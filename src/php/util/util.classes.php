<?php

namespace Util {
    class Classes {
        public static function thatExtend($baseClass) {
            $allClasses = get_declared_classes();
            $extenders = array();
            foreach($allClasses as $class)
                if(is_subclass_of($class, $baseClass)) 
                    array_push($extenders, $class);
            return $extenders;
        }
    }
}

?>