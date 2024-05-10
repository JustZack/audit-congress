<?php

namespace API {
    class MemberByBioguideId extends MemberRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\Members::getByBioguideId($id);
        }
    }
}

?>