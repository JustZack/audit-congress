<?php 

namespace AuditCongress {

    class SessionQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("Sessions");
        }

        public static function getByCongress($congress) {
            $congresses = new SessionQuery();
            $congresses->setSearchColumns(["congress"]);
            $congresses->setSearchValues([$congress]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByChamber($chamber) {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["like"]);
            $congresses->setSearchColumns(["chamber"]);
            $congresses->setSearchValues([$chamber]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByNumber($session) {
            $congresses = new SessionQuery();
            $congresses->setSearchColumns(["number"]);
            $congresses->setSearchValues([$session]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByNumberAndChamber($session, $chamber) {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["like", "="]);
            $congresses->setSearchColumns(["chamber", "number"]);
            $congresses->setSearchValues([$chamber, $session]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressAndNumber($congress, $number) {
            $congresses = new SessionQuery();
            $congresses->setSearchColumns(["congress", "number"]);
            $congresses->setSearchValues([$congress, $number]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressAndChamber($congress, $chamber) {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["=", "like"]);
            $congresses->setSearchColumns(["congress", "chamber"]);
            $congresses->setSearchValues([$congress, $chamber]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressNumberAndChamber($congress, $number, $chamber) {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["=", "=", "like"]);
            $congresses->setSearchColumns(["congress", "number", "chamber"]);
            $congresses->setSearchValues([$congress, $number, $chamber]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByDate($date) {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["<=", ">="]);
            $congresses->setSearchColumns(["startDate", "endDate"]);
            $congresses->setSearchValues([$date, $date]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getCurrent() {
            $congresses = new SessionQuery();
            $congresses->setEqualityOperators(["<=", "like"]);
            $congresses->setSearchColumns(["startDate", "endDate"]);
            $congresses->setSearchValues([date("Y-m-d"), "0000-00-00"]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getAll() {
            $congresses = new SessionQuery();
            $congresses->setOrderBy(["congress", "number"], false);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>