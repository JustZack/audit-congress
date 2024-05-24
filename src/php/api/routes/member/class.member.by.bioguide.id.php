<?php

namespace API {
    class MemberByBioguideId extends MemberRoute {

        public static function parameters() { return ["bioguideId"]; }
        
        public static function fetchResult() {
            $bioguideId = Parameters::get("bioguideId");
            return \AuditCongress\Members::getByBioguideId($bioguideId);
        }
    }
}

?>