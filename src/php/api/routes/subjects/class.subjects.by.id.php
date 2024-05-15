<?php

namespace API {
    class SubjectsById extends SubjectsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillSubjects::getById($id);
        }
    }

    class SubjectsByBillId extends SubjectsRoute {

        public static function parameters() { return ["billId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("billId");
            return \AuditCongress\BillSubjects::getByBillId($id);
        }
    }
}

?>