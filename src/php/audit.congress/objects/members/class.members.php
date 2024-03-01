<?php 

namespace AuditCongress {

    class MemberRow extends \MySqlConnector\SqlRow {
        public
            $bioguideId,
            $thomasId,
            $listId,
            $govTrackId,
            $openSecretsId,
            $voteSmartId,
            $cspanId,
            $mapLightId,
            $icpsrId,
            $wikidata,
            $googleEntityId,

            $OfficialFullName,
            $FirstName,
            $MiddleName,
            $LastName,
            $Gender,
            $BirthYear,
            $DeathYear,
            $imageUrl,
            $imageAttribution,
            $lastUpdate,
            $nextUpdate;
    
        public function getColumns() {
            return ["bioguideId","thomasId","listId","govTrackId",
            "openSecretsId","voteSmartId","cspanId","mapLightId","icpsrId",
            "wikidata","googleEntityId","OfficialFullName","FirstName",
            "MiddleName","LastName","Gender","BirthYear","DeathYear",
            "imageUrl","imageAttribution","lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguideId,$this->thomasId,$this->listId,$this->govTrackId,
            $this->openSecretsId,$this->voteSmartId,$this->cspanId,$this->mapLightId,
            $this->icpsrId,$this->wikidata,$this->googleEntityId,
            $this->OfficialFullName,$this->FirstName,$this->MiddleName,
            $this->LastName,$this->Gender,$this->BirthYear,$this->DeathYear,
            $this->imageUrl,$this->imageAttribution,$this->lastUpdate,$this->nextUpdate];
        }
    }

    class Members extends \MySqlConnector\SqlObject {
        private static $tableName = "Members";
        private static ?\MySqlConnector\Table $staticTable = null;
        public function __construct($equalityOperator = "=", $booleanCondition = "AND") {
            parent::__construct(self::$tableName, $equalityOperator, $booleanCondition);
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

            $current = new \UnitedStatesLegislators\CurrentMembers();
            $current->fetchFromApi();
            $historical = new \UnitedStatesLegislators\HistoricalMembers();
            $historical->fetchFromApi();

            $allMembers = array_merge($current->currentMembers, $historical->historicalMembers);

            $table = self::getTable();

            foreach ($allMembers as $person) {
                $terms = $person->getTerms();
                $personArr = array_merge($person->id->toArray(), 
                                         $person->name->toArray(),
                                         $person->bio->toArray());
                var_dump($personArr);
                var_dump($terms);
                return;
                /*$bioId = $person->id->bioguide;
                $social = $personWithSocials->getSocials()->toArray();
                $social["bioguideId"] = $bioId;
                $social["lastUpdate"] = time();
                $social["nextUpdate"] = time()+(60*60*24*7);
                $row = new MemberSocialsRow($social);
                $table->insert($row->getColumns(), $row->getValues());*/
            }
            self::$cacheIsValid = true;
        }
        /*
            Fetch members by their exact bioguideId
        */
        public static function getByBioguideId($bioguideId) {
            $offices = new Members();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["bioguideId"]);
            $offices->setValues([$bioguideId]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.
        */
        public static function getByName($firstName, $middleName = null, $lastName = null) {
            $offices = new Members();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["FirstName", "MiddleName", "LastName"]);
            $offices->setValues([$firstName, $lastName, $middleName]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members with the given gender (M or F at this time)
        */
        public static function getByGender($gender) {
            $offices = new Members();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["Gender"]);
            $offices->setValues([$gender]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members who where born before or on the given birth year
        */
        public static function getBornBy($birthYear) {
            $offices = new Members("<=");
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["BirthYear"]);
            $offices->setValues([$birthYear]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members who where born after the given birth year
        */
        public static function getBornAfter($birthYear) {
            $offices = new Members(">");
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["BirthYear"]);
            $offices->setValues([$birthYear]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members who died by or on the given death year
        */
        public static function getDeadBy($deathYear) {
            $offices = new Members("<=");
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["DeathYear"]);
            $offices->setValues([$deathYear]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members who died after the given death year
        */
        public static function getDeadAfter($deathYear) {
            $offices = new Members(">");
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["DeathYear"]);
            $offices->setValues([$deathYear]);
            return $offices->selectFromDB();
        }
    }
}

?>