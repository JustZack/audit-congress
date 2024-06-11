<?php

namespace API {
    class RouteById extends GenericRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillActions::getById($id);
        }
    }
    class RouteByBillId extends GenericRoute {

        public function __construct($functionToCall) {
            
        }

        public static function parameters() { return ["billId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("billId");
            return \AuditCongress\BillActions::getByBillId($id);
        }
    }
}

?>