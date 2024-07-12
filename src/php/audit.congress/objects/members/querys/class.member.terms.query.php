<?php 

namespace AuditCongress {

    use MySqlConnector\Comparison;

    class MemberTermsQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery;
        
        public function __construct() {
            parent::__construct("MemberTerms");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["start"], false);
        }

        private static function getSingleTermByBioguideId($bioguideId) {
            $terms = new MemberTermsQuery();
            $terms->addSearch("bioguideId", Comparison::EQUALS, $bioguideId);
            $terms->setLimit(1);
            return $terms;
        }

        public static function getLastByBioguideId($bioguideId) {
            $terms = self::getSingleTermByBioguideId($bioguideId);
            $terms->setOrderBy(["end"], false);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getFirstByBioguideId($bioguideId) {
            $terms = self::getSingleTermByBioguideId($bioguideId);
            $terms->setOrderBy(["start"], true);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBioguideIdByType($bioguideId, $type) {
            $terms = new MemberTermsQuery();
            $terms->addSearch("bioguideId", Comparison::EQUALS, $bioguideId);
            $terms->addSearch("type", Comparison::EQUALS, $type);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBioguideIdByState($bioguideId, $state) {
            $terms = new MemberTermsQuery();
            $terms->addSearch("bioguideId", Comparison::EQUALS, $bioguideId);
            $terms->addSearch("state", Comparison::EQUALS, $state);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByState($state, $year = null) {
            $terms = new MemberTermsQuery();
            $terms->addSearch("state", Comparison::LIKE, $state);
            if ($year != null) $terms->addSearch("start", Comparison::LIKE, $year);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByParty($party, $year = null) {
            $terms = new MemberTermsQuery();
            $terms->addSearch("party", Comparison::LIKE, $party);
            if ($year != null) $terms->addSearch("start", Comparison::LIKE, $year);
            return $terms->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>