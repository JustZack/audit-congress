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
                case "array": $filterType = "json"; break;
                default: return $valueString;
            }
            if ($filterType == "json") $value = json_decode($valueString);
            else $value = filter_var($valueString, $filterType);
            return $value;
        }

        //Get the given named url parameter if set, and convert to the given type if set
        public static function getIfSet($parameter, $type=null) {
            $value = null;
            if (isset($_GET[$parameter])) {
                $value = Parameters::convert($_GET[$parameter], $type);
                if ($type != "bool" && $value == false) $value = null;
            }
            return $value;
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

        public static function get($parameter) { return self::getIfSet($parameter); }
        public static function getBool($parameter) { return self::getIfSet($parameter, "bool"); }
        public static function getInt($parameter) { return self::getIfSet($parameter, "int"); }
        public static function getFloat($parameter) { return self::getIfSet($parameter, "float"); }
        public static function getArray($parameter) { return self::getIfSet($parameter, "array"); }
    }
}

?>