<?php

namespace API {
    class OfficesByOfficeId extends OfficesRoute {

        public static function parameters() { return ["officeid"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberOffices::getByOfficeId($args["officeid"]);
        }
    }
}

?>