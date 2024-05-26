<?php 

namespace AuditCongress {

    class BillSubjects extends BillTable {

        use GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillSubjects", "BillSubjectsQuery", "BillSubjectRow");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new BillSubjects();
            return self::$billsObject;
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subject = null) {
            self::enforceCache();
            $subjects = BillSubjectsQuery::getByFilter($congress, $type, $number, $subject);
            return self::parseResult($subjects);
        }
    }
}

?>