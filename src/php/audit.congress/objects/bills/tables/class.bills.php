<?php 

namespace AuditCongress {

    class Bills extends BillTable {

        use GetById, GetByBioguideId;

        private function __construct() {
            parent::__construct("Bills", "BillsQuery", "BillRow");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new Bills();
            return self::$billsObject;
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = ["updated"]) {
            self::enforceCache();
            $bills = BillsQuery::getByFilter($congress, $type, $number, $title, $sort);
            return self::parseResult($bills);
        }
    }
}

?>