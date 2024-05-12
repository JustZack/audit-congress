<?php

namespace API {
    class TitlesById extends TitlesRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillTitles::getById($id);
        }
    }

    class TitlesByBillId extends TitlesRoute {

        public static function parameters() { return ["billId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("billId");
            return \AuditCongress\BillTitles::getByBillId($id);
        }
    }
}

?>