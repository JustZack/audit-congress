<?php

namespace API {
    class TermsByBioguideId extends TermsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberTerms::getByBioguideId($args["id"]);
        }
    }
}

?>