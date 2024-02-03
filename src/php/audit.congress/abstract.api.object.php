<?php

namespace AuditCongress {

    use Exception;

    abstract class ApiObject {
        use getAndPrintAsJson;
        private $uid;
        //Sub classes must implement the fetch function
        abstract function fetchFromApi();

        //Can inherit or override UID & set functions
        function getUid() { return $this->uid; }
        //Generic setFromApi function to set all class fields with response fields
        function setFromApi($apiRes) { 
            foreach ($apiRes as $key=>$value) $this->{$key} = $value; 
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

    trait getAndPrintAsJson {
        function getAsJson() {
            return json_encode($this);
        }

        function printAsJson() {
            header('Content-Type: application/json');
            print($this->getAsJson());
        }
    }
}
?>