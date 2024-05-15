<?php

namespace API {
    class TermsByBioguideId extends TermsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberTerms::getByBioguideId($id);
        }
    }
}

?>