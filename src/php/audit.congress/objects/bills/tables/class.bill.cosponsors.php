<?php 

namespace AuditCongress {

    class BillCosponsors extends BillTable {

        use GetById, BillsGetByBillId, GetByBioguideId;

        private function __construct() {
            parent::__construct("BillCosponsors", "\AuditCongress\BillCosponsorsQuery");
        }

        private static $billsObject = null;
        public static function getInstance() {
            if (self::$billsObject == null) 
                self::$billsObject = new BillCosponsors();
            return self::$billsObject;
        }

        protected static function parseResult($rows) {
            $rows = BillCosponsorRow::rowsToObjects($rows);
            return $rows;
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $bioguideId = null, $sort = ["sponsoredAt"]) {
            self::enforceCache();
            $cosponsors = BillCosponsorsQuery::getByFilter($congress, $type, $number, $bioguideId, $sort);
            return self::parseResult($cosponsors);
        }
    }
}

?>