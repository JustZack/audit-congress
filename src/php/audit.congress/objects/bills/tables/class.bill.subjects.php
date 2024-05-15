<?php 

namespace AuditCongress {

    class BillSubjects extends BillTable {

        private function __construct() {
            parent::__construct("BillSubjects");
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

        public static function getById($billSubjectId) {
            self::enforceCache();
            $subject = BillSubjectsQuery::getById($billSubjectId);
            return self::returnFirst(self::parseResult($subject));
        }

        public static function getByBillId($billId) {
            self::enforceCache();
            $subjects = BillSubjectsQuery::getByBillId($billId);
            return self::parseResult($subjects);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subject = null) {
            self::enforceCache();
            $subjects = BillSubjectsQuery::getByFilter($congress, $type, $number, $subject);
            return self::parseResult($subjects);
        }
    }
}

?>