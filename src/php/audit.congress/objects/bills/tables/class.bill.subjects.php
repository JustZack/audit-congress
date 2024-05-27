<?php 

namespace AuditCongress {

    class BillSubjects extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillSubjects", "BillSubjectsQuery", "BillSubjectRow");
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subject = null) {
            self::enforceCache();
            $subjects = BillSubjectsQuery::getByFilter($congress, $type, $number, $subject);
            return self::parseResult($subjects);
        }
    }
}

?>