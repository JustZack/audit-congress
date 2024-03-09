<?php 

namespace AuditCongress {

    class MemberElections extends MemberTable {
        
        private function __construct() {
            parent::__construct("MemberElections");
        }

        public function insertPersonElections($person) {
            $bioguideId = $person->id->bioguide;
            $fecIds = $person->id->fec;
            if (is_array($fecIds)) {
                foreach ($fecIds as $fecId) {
                    $election = self::apiElectionToRow($fecId, $bioguideId);
                    $election = new MemberElectionRow($election);
                    $this->queueInsert($election);
                }
            }
        }

        private static function apiElectionToRow($fecId, $bioguideId) {
            $rowArray = array();
            $rowArray["fecId"] = $fecId;
            $rowArray["bioguideId"] = $bioguideId;
            $rowArray = self::setUpdateTimes($rowArray);
            return $rowArray;
        }

        private static $memberTermsTable = null;
        public static function getInstance() {
            if (self::$memberTermsTable == null) 
                self::$memberTermsTable = new MemberElections();
            return self::$memberTermsTable;
        }

        protected static function parseResult($resultRows) {
            return MemberElectionRow::rowsToObjects($resultRows);
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $elections = MemberElectionsQuery::getByBioguideId($bioguideId);
            return self::parseResult($elections);
        }

        public static function getByFecId($fecId) {
            self::enforceCache();
            $elections = MemberElectionsQuery::getByFecId($fecId);
            return self::returnFirst(self::parseResult($elections));
        }
    }
}

?>