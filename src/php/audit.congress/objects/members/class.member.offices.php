<?php 

namespace AuditCongress {

    use \MySqlConnector\SqlRow;
    use \MySqlConnector\SqlObject;
    use \UnitedStatesLegislators\CurrentDistrictOffices;

    class MemberOfficesRow extends SqlRow {
        public
            $bioguideId,$officeId,
            $address,$suite,$building,$city,$state,$zip,
            $latitude,$longitude,$phone,$fax,
            $lastUpdate,$nextUpdate;

            public function getColumns() {
                return ["bioguideId","officeId","address","suite",
                        "building","city","state","zip","latitude",
                        "longitude","phone","fax","lastUpdate","nextUpdate"];
            }

            public function getValues() {
                return [$this->bioguideId,$this->officeId,$this->address,
                $this->suite,$this->building,$this->city,$this->state,
                $this->zip,$this->latitude,$this->longitude,$this->phone,
                $this->fax,$this->lastUpdate,$this->nextUpdate];
            }
    }

    class MemberOfficesQuery extends SqlObject {
        public function __construct() {
            parent::__construct("MemberOffices");
        }

        public static function getByBioguideId($bioguideId) {
            $offices = new MemberOfficesQuery();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["bioguideId"]);
            $offices->setValues([$bioguideId]);
            return $offices->selectFromDB();
        }

        public static function getByOfficeId($officeId) {
            $offices = new MemberOfficesQuery();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["officeId"]);
            $offices->setValues([$officeId]);
            return $offices->selectFromDB();
        }
    }

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
                    $office = self::apiOfficeToRow($office->toArray(), $bioguideId);
                    $office = self::setUpdateTimes($office);
                    $row = new MemberOfficesRow($office);
                    $this->queueInsert($row);
                }
            }
            $this->commitInsert();
            $this->cacheIsValid = true;
        }

        private static function apiOfficeToRow($rowArray, $bioguideId) {
            $rowArray["bioguideId"] = $bioguideId;
            $rowArray["officeId"] = $rowArray["id"];
            return $rowArray;
        }

        private static $memberOfficesTable = null;
        public static function getInstance() {
            if (self::$memberOfficesTable == null) 
                self::$memberOfficesTable = new MemberOffices();
            return self::$memberOfficesTable;
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            return MemberOfficesQuery::getByBioguideId($bioguideId);
        }

        public static function getByOfficeId($officeId) {
            self::enforceCache();
            return MemberOfficesQuery::getByOfficeId($officeId);
        }
    }
}

?>