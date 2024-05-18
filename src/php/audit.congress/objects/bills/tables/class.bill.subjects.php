<?php 

namespace AuditCongress {

    class BillSubjects extends BillTable {

        use BillsGetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillSubjects", "\AuditCongress\BillSubjectsQuery");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new BillSubjects();
            return self::$billsObject;
        }

        protected static function parseResult($rows) {
            $rows = BillSubjectRow::rowsToObjects($rows);
            return $rows;
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subject = null) {
            self::enforceCache();
            $subjects = BillSubjectsQuery::getByFilter($congress, $type, $number, $subject);
            return self::parseResult($subjects);
        }
    }
}

?>