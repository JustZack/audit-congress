<?php

namespace API {
    class Parameters {

        private $parameterValues = array();
        private function __construct() {
            $this->parameterValues = self::getAll();
        }

        private static $parametersInstance = null;
        public static function getInstance() {
            if (self::$parametersInstance == null)
                self::$parametersInstance = new Parameters();
            return self::$parametersInstance;
        }

        private static function getIfInArray($array, $needle, $asType=null) {
            if (isset($array[$needle])) {
                $val = $array[$needle];
                if(isset($type)) $val = Parameters::convert($val, $type);
                return $val;
            }
            else return null;
        }

        public function get($parameter, $type=null) {
            return self::getIfInArray($this->parameterValues, $parameter, $type);
        }

        private static function throwException($function, $message) {
            throw new \API\Exception("\Api\Parameters::$function $message");
        }
        //Convert the given string value to the given type
        public static function convert($valueString, $type) {
            $filterType = null;
            switch ($type) {
                case "bool": $filterType = FILTER_VALIDATE_BOOLEAN; break;
                case "int": $filterType = FILTER_VALIDATE_INT; break;
                case "double":
                case "decimal":
                case "float": $filterType = FILTER_VALIDATE_FLOAT; break;
            }
            return filter_var($valueString, $filterType);
        }

        //Get the given named url parameter if set, and convert to the given type if set
        public static function getIfSet($parameter, $type=null) {
            return self::getIfInArray($_GET, $parameter, $type);
        }

        //Get the given set of url parameters if set
        public static function getManyIfSet($parameters, $types=null) {
            $nParams = count($parameters); $nTypes = $types==null?0:count($types);
            if ($nTypes > 0 && ($nParams != $nTypes)) 
                Parameters::throwException("getAllIfSet", "Mismatch: Found $nParams but $nTypes.");
            
            $paramValues = array();
            for ($i = 0;$i < count($parameters);$i++) {
                $name = $parameters[$i];
                $type = $nTypes > 0 ? null : $types[$i];
                $value = Parameters::getIfSet($name, $type);
                $paramValues[$name] =  $value;
            }
        }

        public static function getAll() { return $_GET; }
    }
}

?>