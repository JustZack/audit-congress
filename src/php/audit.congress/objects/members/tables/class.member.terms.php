<?php 

namespace AuditCongress {
    
    class MemberTerms extends MemberTable {
        
        private function __construct() {
            parent::__construct("MemberTerms");
        }

        public function insertPersonTerms($person) {
            //TODO: Split member term "start" and "end" into year/month/day columns for easier selecting
            $bioguideId = $person->id->bioguide;
            $terms = $person->getTerms();
            foreach ($terms as $term) {
                $term = self::apiTermToRow($term, $bioguideId);
                $term = new MemberTermRow($term);
                $this->queueInsert($term);
            }
        }

        private static function apiTermToRow($term, $bioguideId) {
            $rowArray = $term->toArray();
            $rowArray["bioguideId"] = $bioguideId;
            $rowArray = self::setUpdateTimes($rowArray);
            return $rowArray;
        }

        private static $memberTermsTable = null;
        public static function getInstance() {
            if (self::$memberTermsTable == null) 
                self::$memberTermsTable = new MemberTerms();
            return self::$memberTermsTable;
        }

        protected static function parseResult($resultRows) {
            return MemberTermRow::rowsToObjects($resultRows);
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $terms = MemberTermsQuery::getByBioguideId($bioguideId);
            return self::parseResult($terms);
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