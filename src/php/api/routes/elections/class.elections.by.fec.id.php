<?php

namespace API {
    class ElectionsByFecId extends ElectionsRoute {

        public static function parameters() { return ["fecid"]; }
        
        public static function fetchResult() {
            $fecid = Parameters::get("fecid");
            return \AuditCongress\MemberElections::getByFecId($fecid);
        }
    }
}

?>