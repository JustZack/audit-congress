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
        }

        public function insertPersonTerms($person) {
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

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            return MemberTermsQuery::getByBioguideId($bioguideId);
        }
    }
}

?>