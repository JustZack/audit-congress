<?php 

namespace AuditCongress {

    class BillCosponsorsQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("BillCosponsors");
        }

        public function applySponsoredAtOrder() {
            $this->setOrderBy(["sponsoredAt"], false);
        }

        public static function getById($id) {
            $cosponsor = self::getWithSearchSelect("id", "=", $id);
            return $cosponsor->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBillId($billid) {
            $cosponsors = self::getWithSearchSelect("billId", "=", $billid);
            $cosponsors->applySponsoredAtOrder();
            $cosponsors->applyPagination();
            return $cosponsors->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBioguideId($bioguideId) {
            $cosponsorships = self::getWithSearchSelect("bioguideId", "=", $bioguideId);
            $cosponsorships->applySponsoredAtOrder();
            $cosponsorships->applyPagination();
            return $cosponsorships->selectFromDB()->fetchAllAssoc();
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