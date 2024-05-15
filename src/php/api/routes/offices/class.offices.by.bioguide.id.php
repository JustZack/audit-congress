<?php

namespace API {
    class OfficesByBioguideId extends OfficesRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberOffices::getByBioguideId($id);
        }
    }
}

?>