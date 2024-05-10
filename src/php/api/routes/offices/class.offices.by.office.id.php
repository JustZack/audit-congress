<?php

namespace API {
    class OfficesByOfficeId extends OfficesRoute {

        public static function parameters() { return ["officeid"]; }
        
        public static function fetchResult() {
            $officeid = Parameters::get("officeid");
            return \AuditCongress\MemberOffices::getByOfficeId($officeid);
        }
    }
}

?>