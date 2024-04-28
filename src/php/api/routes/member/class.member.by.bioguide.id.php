<?php

namespace API {
    class MemberByBioguideId extends Route {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\Members::getByBioguideId($args["id"]);
        }
    }
}

?>