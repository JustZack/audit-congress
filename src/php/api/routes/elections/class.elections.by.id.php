<?php

namespace API {
    
    class ElectionsByBioguideId extends ElectionsRoute {
        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberElections::getByBioguideId($id);
        }
    }
 
    class ElectionsByFecId extends ElectionsRoute {

        public static function parameters() { return ["fecid"]; }
        
        public static function fetchResult() {
            $fecid = Parameters::get("fecid");
            return \AuditCongress\MemberElections::getByFecId($fecid);
        }
    }
}

?>