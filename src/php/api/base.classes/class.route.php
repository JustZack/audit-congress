<?php

namespace API {
    abstract class Route extends ExceptionThrower {

        protected 
            $parameters = null,
            $types = null,
            $className = null,
            $functionName = null;
        
        public function __construct($class, $function, $parameters = [], $types = null) {
            $this->className = $class;
            $this->functionName = $function;
            $this->setParameters($parameters);
            $this->setTypes($types);
        }
        //All API Routes require specific parameters to run
        public function canRun() {
            $result = Parameters::hasAll(static::parameters(), static::types());
            return $result;
        }

        //All API Routes fetch some sort of result - This is the most basic way they might
        public function fetchResult() {
            $params = $this->fetchParameters();
            return ($this->getCallableFunction())(...$params);
        }
        
        public function className() { return $this->className; }
        public function functionName() { return $this->functionName; }
        public function getCallableFunction() { 
            return $this->className() . "::" . $this->functionName();
        }

        //Fetch the required parameters for this route & the types
        public function parameters() { return $this->parameters; }
        public function setParameters($newParams) { $this->parameters = $newParams; }
        public function types() { return $this->types; }
        public function setTypes($newTypes) { $this->types = $newTypes; }

        //Fetch the parameters used by this function
        public function fetchParameters() {
            return Parameters::getManyIfSet(self::parameters(), self::types());
        }

        //General function to be sure a parameter is one of the given values (or null)
        public static function validParameter($passed, $validValues) {
            return $passed == null || ($passed != null && in_array($passed, $validValues));
        }
    }
}

?>