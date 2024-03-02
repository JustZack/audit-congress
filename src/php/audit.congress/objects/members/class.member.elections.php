<?php 

namespace AuditCongress {

    class MemberElectionRow extends \MySqlConnector\SqlRow {
        public
            $fecId,
            $bioguideId,
            $lastUpdate,
            $nextUpdate;

            public function getColumns() {
                return ["fecId","bioguideId",
                "lastUpdate","nextUpdate"];
            }
        
            public function getValues() {
                return [$this->fecId,$this->bioguideId,
                $this->lastUpdate,$this->nextUpdate];
            }
    }

    class MemberElectionsQuery extends \MySqlConnector\SqlObject {
        public function __construct() {
            parent::__construct("MemberElections");
        }

        public static function getByBioguideId($bioguideId) {
            $terms = new MemberElectionsQuery();
            $terms->setSelectColumns(["*"]);
            $terms->setColumns(["bioguideId"]);
            $terms->setValues([$bioguideId]);
            return $terms->selectFromDB();
        }

        public static function getByFecId($fecId) {
            $terms = new MemberElectionsQuery();
            $terms->setSelectColumns(["*"]);
            $terms->setColumns(["fecId"]);
            $terms->setValues([$fecId]);
            return $terms->selectFromDB();
        }
    }

    class MemberElections extends MemberTables {
        
        private function __construct() {
            parent::__construct("MemberElections");
            //Terms table is valid by default b/c its updated by the Members table
            $this->cacheIsValid = true;
        }

        protected function updateCache() { return false; }

        public function insertPersonElections($person) {
            $bioguideId = $person->id->bioguide;
            $fecIds = $person->id->fec;
            if (is_array($fecIds)) {
                foreach ($fecIds as $fecId) {
                    $electionArr = array();
                    $electionArr["fecId"] = $fecId;
                    $electionArr["bioguideId"] = $bioguideId;
                    $electionArr = self::setUpdateTimes($electionArr);
                    $electionArr = new MemberElectionRow($electionArr);
                    $this->queueInsert($electionArr);
                }
            }
        }

        private static $memberTermsTable = null;
        public static function getInstance() {
            if (self::$memberTermsTable == null) 
                self::$memberTermsTable = new MemberElections();
            return self::$memberTermsTable;
        }

        public static function getByBioguideId($bioguideId) {
            return MemberTermsQuery::getByBioguideId($bioguideId);
        }
    }
}

?>