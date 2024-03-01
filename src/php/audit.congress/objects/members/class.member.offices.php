<?php 

namespace AuditCongress {

    class MemberOfficesRow extends \MySqlConnector\SqlRow {
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

    class MemberOffices extends \MySqlConnector\SqlObject {
        private static $tableName = "MemberOffices";
        private static ?\MySqlConnector\Table $staticTable = null;
        public function __construct() {
            parent::__construct(self::$tableName);
            if (self::$staticTable == null) self::$staticTable = $this->table;
            self::enforceCache();
        }

        private function enforceCache() {
            if (!self::cacheIsValid()) self::updateCache();
        }

        public static function getTable() { return self::$staticTable; }

        private static $cacheIsValid = null;
        private static function cacheIsValid() {
            if (self::$cacheIsValid != null) return self::$cacheIsValid;

            $table = self::getTable();
            $topRow = $table->select(["lastUpdate", "nextUpdate"], null, null, 1)->fetchAssoc();
            if ($topRow != null) {
                $next = (int)$topRow["nextUpdate"]-100000000;
                return !($next == false || $next < time());
            } else return false;
        }

        private static function updateCache() {
            //Clear out all data associated with members
            self::getTable()->truncate();

            $offices = new \UnitedStatesLegislators\CurrentDistrictOffices();
            $offices->fetchFromApi();

            $table = self::getTable();
            foreach ($offices->currentOffices as $personWithOffice) {
                $bioId = $personWithOffice->id->bioguide;
                foreach ($personWithOffice->getOffices() as $office) {
                    $office = $office->toArray();
                    $office["bioguideId"] = $bioId;
                    $office["officeId"] = $office["id"];
                    unset($office["id"]);
                    $office["lastUpdate"] = time();
                    $office["nextUpdate"] = time()+(60*60*24*7);
                    $row = new MemberOfficesRow($office);
                    $table->insert($row->getColumns(), $row->getValues());
                }
            }
            self::$cacheIsValid = true;
        }

        public static function getByBioguideId($bioguideId) {
            $offices = new MemberOffices();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["bioguideId"]);
            $offices->setValues([$bioguideId]);
            return $offices->selectFromDB();
        }

        public static function getByOfficeId($officeId) {
            $offices = new MemberOffices();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["officeId"]);
            $offices->setValues([$$officeId]);
            return $offices->selectFromDB();
        }
    }
}

?>