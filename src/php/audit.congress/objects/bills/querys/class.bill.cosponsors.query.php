<?php 

namespace AuditCongress {

    class BillCosponsorsQuery extends BillQuery {
        
        use BillsGetByIdQuery, BillsGetByBillIdQuery, BillsGetByBioguideIdQuery;

        public function __construct() {
            parent::__construct("BillCosponsors");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["sponsoredAt"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $bioguideId = null, $sort = ["sponsoredAt"]) {
            $cosponsors = new BillCosponsorsQuery();

            $cosponsors->setBooleanCondition("AND");
            
            if ($congress != null) $cosponsors->addSearchValue("congress", "=", $congress);
            if ($type != null) $cosponsors->addSearchValue("type", "=", $type);
            if ($number != null) $cosponsors->addSearchValue("number", "=", $number);
            if ($bioguideId != null) $cosponsors->addSearchValue("bioguideId", "=", $bioguideId);

            $cosponsors->setOrderBy($sort, false);
            $cosponsors->applyPagination();
            return $cosponsors->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>