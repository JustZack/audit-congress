<?php 

namespace AuditCongress {

    class MemberTermRow extends \MySqlConnector\SqlRow {
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

    class MemberTermsQuery extends \MySqlConnector\SqlObject {
        public function __construct() {
            parent::__construct("MemberTerms");
        }

        public static function getByBioguideId($bioguideId) {
            $terms = new MemberTermsQuery();
            $terms->setSelectColumns(["*"]);
            $terms->setColumns(["bioguideId"]);
            $terms->setValues([$bioguideId]);
            return $terms->selectFromDB();
        }

        public static function getByBioguideIdByType($bioguideId, $type) {
            $terms = new MemberTermsQuery();
            $terms->setSelectColumns(["*"]);
            $terms->setColumns(["bioguideId", "type"]);
            $terms->setValues([$bioguideId, $type]);
            return $terms->selectFromDB();
        }

        public static function getByBioguideIdByState($bioguideId, $state) {
            $terms = new MemberTermsQuery();
            $terms->setSelectColumns(["*"]);
            $terms->setColumns(["bioguideId", "state"]);
            $terms->setValues([$bioguideId, $state]);
            return $terms->selectFromDB();
        }
    }

    class MemberTerms extends MemberTable {
        
        private function __construct() {
            parent::__construct("MemberTerms");
            //Terms table is valid by default b/c its updated by the Members table
            $this->cacheIsValid = true;
        }

        public function beforeUpdateCache() { return false; }

        public function updateCache() { return false; }

        public function insertPersonTerms($person) {
            $bioguideId = $person->id->bioguide;
            $terms = $person->getTerms();
            foreach ($terms as $term) {
                $termArr = $term->toArray();
                $termArr["bioguideId"] = $bioguideId;
                $termArr = self::setUpdateTimes($termArr);
                $termRow = new MemberTermRow($termArr);
                $this->queueInsert($termRow);
            }
        }

        private static $memberTermsTable = null;
        public static function getInstance() {
            if (self::$memberTermsTable == null) 
                self::$memberTermsTable = new MemberTerms();
            return self::$memberTermsTable;
        }

        public static function getByBioguideId($bioguideId) {
            return MemberTermsQuery::getByBioguideId($bioguideId);
        }
    }
}

?>