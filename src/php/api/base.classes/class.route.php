<?php

namespace API {
    abstract class Route extends ExceptionThrower {
        //All API Routes require specific parameters to run
        public static function canRun() {
            $result = Parameters::hasAll(static::parameters(), static::types());
            return $result;
        }
        //All API Routes fetch some sort of result
        public abstract static function fetchResult();
        
        //Fetch the parameters and their types
        public abstract static function parameters();
        public static function types() { return null; }

        //Fetch the parameters used by this function
        public static function fetchParameters() {
            return Parameters::getManyIfSet(static::parameters(), static::types());
        }

        //General function to be sure a parameter is one of the given values (or null)
        public static function validParameter($passed, $validValues) {
            return $passed == null || ($passed != null && in_array($passed, $validValues));
        }
    }
}

?>