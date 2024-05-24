<?php

namespace API {
    class OfficesByBioguideId extends OfficesRoute {

        public static function parameters() { return ["bioguideId"]; }
        
        public static function fetchResult() {
            $bioguideId = Parameters::get("bioguideId");
            return \AuditCongress\MemberOffices::getByBioguideId($bioguideId);
        }
    }
}

?>