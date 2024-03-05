<?php 

namespace AuditCongress {

    use \MySqlConnector\SqlRow;
    use \MySqlConnector\SqlObject;

    class MemberTermRow extends SqlRow {
        public
            $bioguideId,
            $type,
            $start,
            $end,
            $state,
            $district,
            $party,
            $class,
            $how,

            $state_rank,
            $url,
            $rss_url,
            $contact_form,
            $address,
            $office,
            $phone,

            $lastUpdate,
            $nextUpdate;

            public function getColumns() {
                return ["bioguideId","type","start","end",
                "state","district","party","class","how","state_rank",
                "url","rss_url","contact_form","address","office","phone",
                "lastUpdate","nextUpdate"];
            }
        
            public function getValues() {
                return [$this->bioguideId,$this->type,$this->start,$this->end,
                $this->state,$this->district,$this->party,$this->class,
                $this->how,$this->state_rank,$this->url,$this->rss_url,
                $this->contact_form,$this->address,$this->office,$this->phone,
                $this->lastUpdate,$this->nextUpdate];
            }
    }

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