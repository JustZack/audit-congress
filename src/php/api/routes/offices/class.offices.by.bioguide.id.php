<?php

namespace API {
    class OfficesByBioguideId extends OfficesRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberOffices::getByBioguideId($args["id"]);
        }
    }
}

?>