<?php 

namespace AuditCongress {

    class BillCosponsors extends BillTable {

        private function __construct() {
            parent::__construct("BillCosponsors");
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

        public static function getById($billCosponsorId) {
            self::enforceCache();
            $cosponsor = BillCosponsorsQuery::getById($billCosponsorId);
            return self::returnFirst(self::parseResult($cosponsor));
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $bioguideId = null, $sort = ["sponsoredAt"]) {
            self::enforceCache();
            $cosponsors = BillCosponsorsQuery::getByFilter($congress, $type, $number, $bioguideId, $sort);
            return self::parseResult($cosponsors);
        }
    }
}

?>