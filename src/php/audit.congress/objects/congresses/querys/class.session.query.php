<?php 

namespace AuditCongress {

    use MySqlConnector\Comparison;

    class SessionQuery extends AuditCongressQuery {
        use TruncateRowsQuery, InsertQueueingQuery;

        public function __construct() {
            parent::__construct("Sessions");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["congress", "chamber", "number"], false);
        }

        public static function getByCongress($congress) {
            $congresses = new SessionQuery();
            $congresses->addSearch("congress", Comparison::EQUALS, $congress);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByChamber($chamber) {
            $congresses = new SessionQuery();
            $congresses->addSearch("chamber", Comparison::LIKE, $chamber);
            $congresses->applyPagination();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByNumber($session) {
            $congresses = new SessionQuery();
            $congresses->addSearch("number", Comparison::EQUALS, $session);
            $congresses->applyPagination();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByNumberAndChamber($session, $chamber) {
            $congresses = new SessionQuery();
            $congresses->addSearch("chamber", Comparison::LIKE, $chamber);
            $congresses->addSearch("number", Comparison::EQUALS, $session);
            $congresses->applyPagination();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressAndNumber($congress, $number) {
            $congresses = new SessionQuery();
            $congresses->addSearch("congress", Comparison::EQUALS, $congress);
            $congresses->addSearch("number", Comparison::EQUALS, $number);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressAndChamber($congress, $chamber) {
            $congresses = new SessionQuery();
            $congresses->addSearch("chamber", Comparison::LIKE, $chamber);
            $congresses->addSearch("congress", Comparison::EQUALS, $congress);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressNumberAndChamber($congress, $number, $chamber) {
            $congresses = new SessionQuery();
            $congresses->addSearch("congress", Comparison::EQUALS, $congress);
            $congresses->addSearch("number", Comparison::EQUALS, $number);
            $congresses->addSearch("chamber", Comparison::LIKE, $chamber);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByDate($date) {
            $congresses = new SessionQuery();
            $congresses->addSearch("startDate", Comparison::LESS_THAN_EQUALS, $date);
            $congresses->addSearch("endDate", Comparison::GREATER_THAN_EQUALS, $date);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getCurrent() {
            $congresses = new SessionQuery();
            $congresses->addSearch("endDate", Comparison::EQUALS, "0000-00-00");
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getAll() {
            $congresses = new SessionQuery();
            $congresses->setOrderBy(["congress", "number"], false);
            $congresses->applyPagination();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>