<?php 

namespace AuditCongress {

    class BillTitles extends BillTable {

        use BillsGetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillTitles", "\AuditCongress\BillTitlesQuery");
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

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null) {
            self::enforceCache();
            $titles = BillTitlesQuery::getByFilter($congress, $type, $number, $title);
            return self::parseResult($titles);
        }
    }
}

?>