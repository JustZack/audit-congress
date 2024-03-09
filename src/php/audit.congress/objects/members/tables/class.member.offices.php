<?php 

namespace AuditCongress {

    use \UnitedStatesLegislators\CurrentDistrictOffices;

    class MemberOffices extends MemberTable {

        private function __construct() {
            parent::__construct("MemberOffices");
        }

        public function updateCache() {
            var_dump("Update cache for: ".$this->name);

            //Clear out all data associated with socials
            $this->clearRows();

            $offices = new CurrentDistrictOffices();

            foreach ($offices->currentOffices as $personWithOffice) {
                $bioguideId = $personWithOffice->id->bioguide;
                foreach ($personWithOffice->getOffices() as $office) {
                    $office = self::apiOfficeToRow($office, $bioguideId);
                    $row = new MemberOfficesRow($office);
                    $this->queueInsert($row);
                }
            }
            $this->commitInsert();
            $this->cacheIsValid = true;
        }

        private static function apiOfficeToRow($office, $bioguideId) {
            $rowArray = $office->toArray();
            $rowArray["bioguideId"] = $bioguideId;
            $rowArray["officeId"] = $rowArray["id"];
            $rowArray = self::setUpdateTimes($rowArray);
            return $rowArray;
        }

        private static $memberOfficesTable = null;
        public static function getInstance() {
            if (self::$memberOfficesTable == null) 
                self::$memberOfficesTable = new MemberOffices();
            return self::$memberOfficesTable;
        }

        protected static function parseResult($resultRows) {
            return MemberOfficesRow::rowsToObjects($resultRows);
        }

        public static function getByBioguideId($bioguideId) {
            $offices = MemberOfficesQuery::getByBioguideId($bioguideId);
            return self::parseResult($offices);
        }

        public static function getByOfficeId($officeId) {
            self::enforceCache();
            $offices = MemberOfficesQuery::getByOfficeId($officeId);
            return self::parseResult($offices);
        }
    }
}

?>