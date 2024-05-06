<?php 

namespace AuditCongress {

    class Bills extends BillTable {

        private function __construct() {
            parent::__construct("Bills");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new Bills();
            return self::$billsObject;
        }

        protected static function parseResult($rows) {
            $rows = BillRow::rowsToObjects($rows);
            return $rows;
        }

        public static function getById($billId) {
            self::enforceCache();
            $bill = BillsQuery::getById($billId);
            return self::returnFirst(self::parseResult($bill));
        }

        public static function getByCongressTypeNumber($congress, $type, $number) {
            self::enforceCache();
            $bill = BillsQuery::getByCongressTypeNumber($congress, $type, $number);
            return self::returnFirst(self::parseResult($bill));
        }

        public static function getByCongressAndType($congress, $type) {
            self::enforceCache();
            $bill = BillsQuery::getByCongressAndType($congress, $type);
            return self::parseResult($bill);
        }
    }
}

?>