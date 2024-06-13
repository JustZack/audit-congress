<?php

namespace API {
    class Route extends ExceptionThrower {

        protected 
            $required = null,
            $optional = null,
            $className = null,
            $functionName = null;
        
        /*
            @$class is the base classname this route uses
            @$function is the function from the class this route uses
            @$requireParams is an assoc array of parameter name -> type mapping of required parameters
            @$optionalParams is an assoc array of parameter name -> type mapping of optional parameters
        */
        public function __construct($class, $function, $requireParams = [], $optionalParams = []) {
            $this->className = $class;
            $this->functionName = $function;
            $this->setRequired($requireParams);
            $this->setOptional($optionalParams);
        }
        //All API Routes require specific parameters to run
        public function canRun($required = true) {
            $result = Parameters::hasAll(static::parameters($required), static::types($required));
            return $result;
        }

        //All API Routes fetch some sort of result - This is the most direct way it could
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
        public function required()      { return array_keys($this->required); }
        public function requiredTypes() { return array_values($this->optional); }
        public function setRequired($required) { $this->required = $required; }
        //Fetch the optional parameters for this route & the types
        public function optional()      { return array_keys($this->optional); }
        public function optionalTypes() { return array_values($this->optional); }
        public function setOptional($optional) { $this->optional = $optional; }

        public function parameters($required = false) {
            if ($required) return $this->required();
            else return $this->optional();
        }

        public function types($required = false) {
            if ($required) return $this->requiredTypes();
            else return $this->optionalTypes();
        }

        //Fetch the parameters used by this function
        public function fetchParameters($required = true) {
            return Parameters::getManyIfSet(self::parameters($required), self::types($required));
        }

        //General function to be sure a parameter is one of the given values (or null)
        public static function validParameter($passed, $validValues) {
            return $passed == null || ($passed != null && in_array($passed, $validValues));
        }
    }
}

?>