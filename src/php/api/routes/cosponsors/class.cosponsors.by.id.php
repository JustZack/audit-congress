<?php

namespace API {
    class CosponsorsById extends CosponsorsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillCosponsors::getById($id);
        }
    }
}

?>