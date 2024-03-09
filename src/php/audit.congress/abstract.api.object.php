<?php

namespace AuditCongress {
    abstract class ApiObject {
        use getAndPrintAsJson;
        use lowerCaseField;
        use setFieldsFromObject;

        public 
            $uid,
            $route;
        //Sub classes must implement the fetch function
        abstract function fetchFromApi();

        //Can inherit or override UID & set functions
        function setUidFromRoute() { $this->uid = str_replace("/", ".", $this->route); }
        function getUid() { return $this->uid; }
        //Generic setFromApi function to set all class fields with response fields
        function setFromApi($apiRes) { 
            $this->setFieldsFromObject($apiRes);
        }
        //Generic setFromApi function to set specific class field with array, and optionally as the specified object
        function setFromApiAsArray($apiRes, $destinationKey, $objectType = null) { 
            $this->setFromApiAsAnyArray($apiRes, $destinationKey, $objectType, false);
        }
        //Generic setFromApi function to set specific class field with an associative array, and optionally as the specified object
        function setFromApiAsAssocArray($apiRes, $destinationKey, $objectType = null) { 
            $this->setFromApiAsAnyArray($apiRes, $destinationKey, $objectType, true);
        }
        //Private function to handle setting as an (associative) array
        private function setFromApiAsAnyArray($apiRes, $destinationKey, $objectType = null, $associative) {
            $toSet = array();
            foreach ($apiRes as $key=>$value) {                              
                if ($associative == true) {
                    if ($objectType == null) $toSet[$key] = $value; 
                    else $toSet[$key] = new $objectType($value); 
                } else {
                    if ($objectType == null) array_push($toSet, $value); 
                    else array_push($toSet, new $objectType($value)); 
                }
            }
            $this->{$destinationKey} = $toSet;
        }
    }

    class ApiChildObject {
        use lowerCaseField;
        use setFieldsFromObject;
        use unsetField;
        use toArray;
    }

    trait getAndPrintAsJson {
        function getAsJson() {
            return json_encode($this);
        }

        function printAsJson() {
            header('Content-Type: application/json');
            print($this->getAsJson());
        }
    }

    trait lowerCaseField {
        function lowerCaseField($fieldName) {
            $this->{$fieldName} = strtolower($this->{$fieldName});
        }
    }

    trait unsetField {
        function unsetField($fieldName) {
            unset($this->{$fieldName});
        }
    }

    trait setFieldsFromObject {
        function setFieldsFromObject($obj) {
            foreach ($obj as $key=>$value) $this->{$key} = $value; 
        }
    }

    trait toArray {
        function toArray() {
            return json_decode(json_encode($this), true);
        }
    }
}
?>