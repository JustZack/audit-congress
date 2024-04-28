<?php

namespace API {
    class Parameters extends ExceptionThrower {

        public static function has($parameter, $type=null) {
            return Parameters::getIfSet($parameter, $type) != null;
        }

        public static function hasAll($parameters, $types=null) {
            $paramSet = Parameters::getManyIfSet($parameters, $types);
            foreach ($paramSet as $key=>$value) if ($value == null) return false;
            return true;
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
                default: return $valueString;
            }
            return filter_var($valueString, $filterType);
        }

        //Get the given named url parameter if set, and convert to the given type if set
        public static function getIfSet($parameter, $type=null) {
            if (isset($_GET[$parameter])) {
                $val = $_GET[$parameter];
                if(isset($type)) {
                    $val = Parameters::convert($val, $type);
                    if ($type != "bool" && $val == false) return null;
                }
                return $val;
            }
            else return null;
        }

        //Get the given set of url parameters if set
        public static function getManyIfSet($parameters, $types=null) {
            $nParams = count($parameters); $nTypes = $types==null?0:count($types);
            if ($nTypes > 0 && ($nParams != $nTypes)) 
                Parameters::throwException("getManyIfSet", "Mismatch: Found $nParams but $nTypes.");
            
            $paramValues = array();
            for ($i = 0;$i < count($parameters);$i++) {
                $name = $parameters[$i];
                $type = $nTypes == 0 ? null : $types[$i];
                $value = Parameters::getIfSet($name, $type);
                $paramValues[$name] =  $value;
            }

            return $paramValues;
        }

        public static function getAll() { return $_GET; }
    }
}

?>