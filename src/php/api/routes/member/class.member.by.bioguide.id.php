<?php

namespace API {
    class MemberByBioguideId extends MemberRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $bioguideId = Parameters::get("id");
            return \AuditCongress\Members::getByBioguideId($bioguideId);
        }
    }
}

?>