<?php 

namespace AuditCongress {

    class BillTitles extends BillTable {

        use GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillTitles", "BillTitlesQuery", "BillTitleRow");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new BillTitles();
            return self::$billsObject;
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null) {
            self::enforceCache();
            $titles = BillTitlesQuery::getByFilter($congress, $type, $number, $title);
            return self::parseResult($titles);
        }
    }
}

?>