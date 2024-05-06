<?php

namespace API {
    class BillsById extends BillsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\Bills::getById($args["id"]);
        }
    }
}

?>