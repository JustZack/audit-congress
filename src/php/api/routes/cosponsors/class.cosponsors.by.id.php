<?php

namespace API {
    class CosponsorsById extends CosponsorsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::getIfSet("id");
            return \AuditCongress\BillCosponsors::getById($id);
        }
    }
}

?>