<?php

namespace API {
    class BillsById extends BillsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\Bills::getById($id);
        }
    }
}

?>