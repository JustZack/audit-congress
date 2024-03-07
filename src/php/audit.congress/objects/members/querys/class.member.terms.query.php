<?php 

namespace AuditCongress {

    use \MySqlConnector\SqlObject;

    class MemberTermsQuery extends SqlObject {
        public function __construct() {
            parent::__construct("MemberTerms");
        }

        public static function getByBioguideId($bioguideId) {
            $terms = new MemberTermsQuery();
            $terms->setSearchColumns(["bioguideId"]);
            $terms->setSearchValues([$bioguideId]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        private static function getSingleTermByBioguideId($bioguideId) {
            $terms = new MemberTermsQuery();
            $terms->setSearchColumns(["bioguideId"]);
            $terms->setSearchValues([$bioguideId]);
            $terms->setLimit(1);
            return $terms;
        }

        public static function getLastByBioguideId($bioguideId) {
            $terms = self::getSingleTermByBioguideId($bioguideId);
            $terms->setOrderBy("end", false);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getFirstByBioguideId($bioguideId) {
            $terms = self::getSingleTermByBioguideId($bioguideId);
            $terms->setOrderBy("start", true);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBioguideIdByType($bioguideId, $type) {
            $terms = new MemberTermsQuery();
            $terms->setSearchColumns(["bioguideId", "type"]);
            $terms->setSearchValues([$bioguideId, $type]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBioguideIdByState($bioguideId, $state) {
            $terms = new MemberTermsQuery();
            $terms->setSearchColumns(["bioguideId", "state"]);
            $terms->setSearchValues([$bioguideId, $state]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByState($state, $year = null) {
            $terms = new MemberTermsQuery();
            $terms->setEqualityOperator("like");
            $terms->setSearchColumns(["state", "start"]);
            $terms->setSearchValues([$state, $year]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByParty($party, $year = null) {
            $terms = new MemberTermsQuery();
            $terms->setEqualityOperator("like");
            $terms->setSearchColumns(["party", "start"]);
            $terms->setSearchValues([$party, $year]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>