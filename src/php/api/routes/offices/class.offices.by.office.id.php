<?php

namespace API {
    class OfficesByOfficeId extends OfficesRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberOffices::getById($id);
        }
    }
}

?>