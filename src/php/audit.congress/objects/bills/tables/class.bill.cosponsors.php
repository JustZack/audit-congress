<?php 

namespace AuditCongress {

    class BillCosponsors extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId, GetByBioguideId;

        private function __construct() {
            parent::__construct("BillCosponsors", "BillCosponsorsQuery", "BillCosponsorRow");
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $bioguideId = null, $sort = ["sponsoredAt"]) {
            self::enforceCache();
            $cosponsors = BillCosponsorsQuery::getByFilter($congress, $type, $number, $bioguideId, $sort);
            return self::parseResult($cosponsors);
        }
    }
}

?>