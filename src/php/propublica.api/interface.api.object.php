<?php

namespace ProPublica {
    interface ProPublicaApiObject {
        function fetchFromApi();
        function setFromApi($apiRes);

        function getUid();
    }

    trait getAndPrintAsJson {
        function getAsJson() {
            json_encode($this);
        }

        function printAsJson() {
            header('Content-Type: application/json');
            print(json_encode($this));
        }
    }
}
?>