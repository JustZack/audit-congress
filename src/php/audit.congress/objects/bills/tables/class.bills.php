<?php 

namespace AuditCongress {

    class Bills extends BillTable {

        use BillsGetById, BillsGetByBioguideId;

        private function __construct() {
            parent::__construct("Bills", "\AuditCongress\BillsQuery");
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

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = ["updated"]) {
            self::enforceCache();
            $bills = BillsQuery::getByFilter($congress, $type, $number, $title, $sort);
            return self::parseResult($bills);
        }
    }
}

?>