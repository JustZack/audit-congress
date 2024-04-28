<?php

namespace API {
    class MemberByBioguideId extends MemberRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\Members::getByBioguideId($args["id"]);
        }
    }
}

?>