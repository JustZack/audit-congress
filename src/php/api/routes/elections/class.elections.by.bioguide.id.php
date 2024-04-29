<?php

namespace API {
    class ElectionsByBioguideId extends ElectionsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberElections::getByBioguideId($args["id"]);
        }
    }
}

?>