<?php

namespace API {
    class ElectionsByFecId extends ElectionsRoute {

        public static function parameters() { return ["fecid"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberElections::getByFecId($args["fecid"]);
        }
    }
}

?>