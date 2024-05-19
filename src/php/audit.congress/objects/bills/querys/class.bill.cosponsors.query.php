<?php 

namespace AuditCongress {

    class BillCosponsorsQuery extends AuditCongressQuery {

        use BillsGetByIdQuery, BillsGetByBillIdQuery, GetByBioguideIdQuery, BillsGetWithFilterQuery;

        public function __construct() {
            parent::__construct("BillCosponsors");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["sponsoredAt"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $bioguideId = null, $sort = ["sponsoredAt"]) {
            $cosponsors = self::getWithFilterPlusOne($congress, $type, $number, "bioguideId", $bioguideId);
            $cosponsors->setOrderBy($sort, false);
            return $cosponsors->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>