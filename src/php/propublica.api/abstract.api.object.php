<?php

namespace ProPublica {
    abstract class ApiObject {
        use getAndPrintAsJson;
        //Sub classes must implement a fetch and get UID function
        abstract function fetchFromApi();
        abstract function getUid();
        //Generic setFromApi function that works with most sub classes
        function setFromApi($apiRes) { foreach ($apiRes as $key=>$value) $this->{$key} = $value; }
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