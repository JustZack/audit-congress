<?php

namespace API {
    class OfficesByOfficeId extends OfficesRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberOffices::getById($id);
        }
    }

    class OfficesByBioguideId extends OfficesRoute {

        public static function parameters() { return ["bioguideId"]; }
        
        public static function fetchResult() {
            $bioguideId = Parameters::get("bioguideId");
            return \AuditCongress\MemberOffices::getByBioguideId($bioguideId);
        }
    }
}

?>