<?php

namespace API {
    class ActionsById extends ActionsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillActions::getById($id);
        }
    }
    class ActionsByBillId extends ActionsRoute {

        public static function parameters() { return ["billId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("billId");
            return \AuditCongress\BillActions::getByBillId($id);
        }
    }
}

?>