<?php

namespace AuditCongress {
    class ACException extends \Exception {
        function __construct($errorMessage) {
            \Exception::__construct("Audit Congress Exception: $errorMessage");
        }
    }
}

?>