<?php

namespace AuditCongress {

    use Exception;

    abstract class ApiObject {
        use getAndPrintAsJson;
        use lowerCaseField;
        use setFieldsFromObject;
        private $uid;
        //Sub classes must implement the fetch function
        abstract function fetchFromApi();

        //Can inherit or override UID & set functions
        function getUid() { return $this->uid; }
        //Generic setFromApi function to set all class fields with response fields
        function setFromApi($apiRes) { 
            $this->setFieldsFromObject($apiRes);
        }
        //Generic setFromApi function to set specific class field with array, and optinally as the specified object
        function setFromApiAsArray($apiRes, $destinationKey, $objectType = null) { 
            $toSet = array();
            foreach ($apiRes as $key=>$value) {
                if ($objectType == null) array_push($toSet, $value); 
                else array_push($toSet, new $objectType($value)); 
            }
            $this->{$destinationKey} = $toSet;
        }
    }

    class ApiChildObject {
        use lowerCaseField;
        use setFieldsFromObject;
        use unsetField;
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
}
?>