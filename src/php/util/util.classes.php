<?php

namespace Util {
    class Classes {
        public static function getInheritance($base, $extends = true) {
            $allClasses = get_declared_classes();
            $extenders = array();
            $function = $extends ? "is_subclass_of" : "class_implements";
            foreach($allClasses as $class)
                if($function($class, $base)) 
                    array_push($extenders, $class);
            return $extenders;
        }

        public static function thatExtend($baseClass) {
            return self::getInheritance($baseClass, true);
        }
        
        public static function thatImplement($baseInterface) {
            return self::getInheritance($baseInterface, false);
        }
    }
}

?>