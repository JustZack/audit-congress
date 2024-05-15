<?php 

namespace AuditCongress {

    class BillTitles extends BillTable {

        private function __construct() {
            parent::__construct("BillTitles");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new BillTitles();
            return self::$billsObject;
        }

        protected static function parseResult($rows) {
            $rows = BillTitleRow::rowsToObjects($rows);
            return $rows;
        }

        public static function getById($billTitleId) {
            self::enforceCache();
            $title = BillTitlesQuery::getById($billTitleId);
            return self::returnFirst(self::parseResult($title));
        }

        public static function getByBillId($billId) {
            self::enforceCache();
            $titles = BillTitlesQuery::getByBillId($billId);
            return self::parseResult($titles);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null) {
            self::enforceCache();
            $titles = BillTitlesQuery::getByFilter($congress, $type, $number, $title);
            return self::parseResult($titles);
        }
    }
}

?>