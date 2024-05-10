<?php

namespace API {
    class ElectionsByBioguideId extends ElectionsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberElections::getByBioguideId($id);
        }
    }
}

?>