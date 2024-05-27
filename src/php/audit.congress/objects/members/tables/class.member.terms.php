<?php 

namespace AuditCongress {
    
    class MemberTerms extends MemberTable {
        use \Util\GetInstance, GetByBioguideId;

        private function __construct() {
            parent::__construct("MemberTerms", "MemberTermsQuery", "MemberTermRow");
        }

        public static function getLastByBioguideId($bioguideId) {
            self::enforceCache();
            $terms = MemberTermsQuery::getLastByBioguideId($bioguideId);
            return self::returnFirst(self::parseResult($terms));
        }

        public static function getFirstByBioguideId($bioguideId) {
            self::enforceCache();
            $terms = MemberTermsQuery::getFirstByBioguideId($bioguideId);
            return self::returnFirst(self::parseResult($terms));
        }

        public static function getByBioguideIdByType($bioguideId, $type) {
            self::enforceCache();
            $terms = MemberTermsQuery::getByBioguideIdByType($bioguideId, $type);
            return self::parseResult($terms);
        }

        public static function getByBioguideIdByState($bioguideId, $stateAbbr) {
            self::enforceCache();
            $terms = MemberTermsQuery::getByBioguideIdByState($bioguideId, $stateAbbr);
            return self::parseResult($terms);
        }

        public static function getByState($state, $year = null) {
            self::enforceCache();
            $terms = MemberTermsQuery::getByState($state, $year);
            return self::parseResult($terms);
        }

        public static function getByParty($party, $year = null) {
            self::enforceCache();
            $terms = MemberTermsQuery::getByParty($party, $year);
            return self::parseResult($terms);
        }
    }
}

?>