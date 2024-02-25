<?php

namespace MySqlConnector {
    class SqlException extends \Exception {
        function __construct($errorMessage) {
            \Exception::__construct("Sql Operation failed: $errorMessage");
        }
    }
}

?>