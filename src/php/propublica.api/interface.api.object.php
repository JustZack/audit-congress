<?php

namespace ProPublica {
    interface ApiObject {
        function fetchFromApi();
        function setFromApi($apiRes);
        function getUid();
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