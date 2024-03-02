<?php 

namespace AuditCongress {

    class MemberRow extends \MySqlConnector\SqlRow {
        public
            $bioguide,
            $thomas,
            $lis,
            $govtrack,
            $opensecrets,
            $votesmart,
            $cspan,
            $maplight,
            $icpsr,
            $wikidata,
            $google_entity_id,

            $official_full,
            $first,
            $last,
            $gender,
            $birthday,

            $imageUrl,
            $imageAttribution,
            $lastUpdate,
            $nextUpdate;
    
        public function getColumns() {
            return ["bioguideId","thomasId","lisId","govTrackId",
            "openSecretsId","voteSmartId","cspanId","mapLightId","icpsrId",
            "wikidata","google_entity_id","official_full","first","last",
            "gender","birthday","imageUrl","imageAttribution",
            "lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguide,$this->thomas,$this->lis,$this->govtrack,
            $this->opensecrets,$this->votesmart,$this->cspan,$this->maplight,
            $this->icpsr,$this->wikidata,$this->google_entity_id,
            $this->official_full,$this->first,$this->last,
            $this->gender,$this->birthday,$this->imageUrl,$this->imageAttribution,
            $this->lastUpdate,$this->nextUpdate];
        }
    }
    class MembersQuery extends \MySqlConnector\SqlObject {
        public function __construct() {
            parent::__construct("Members");
        }

        public static function getByBioguideId($bioguideId) {
            $offices = new MembersQuery();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["bioguideId"]);
            $offices->setValues([$bioguideId]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.
        */
        public static function getByName($firstName = null, $lastName = null) {
            $offices = new MembersQuery();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["first", "last"]);
            $offices->setValues([$firstName, $lastName]);
            return $offices->selectFromDB();
        }

        /*
            Fetch members with the given gender (M or F at this time)
        */
        public static function getByGender($gender) {
            $offices = new MembersQuery();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["gender"]);
            $offices->setValues([$gender]);
            return $offices->selectFromDB();
        }
    }

    class Members extends MemberTables {
        
        private function __construct() {
            parent::__construct("Members");
        }

        protected function updateCache() {
            //Get instances for member data within this route
            $memberTerms = MemberTerms::getInstance();
            $memberElections = MemberElections::getInstance();
            
            //Clear out all data associated with members
            $memberTerms->clearRows();
            $memberElections->clearRows();
            $this->clearRows();

            //Update the cache for member data outside this route
            MemberOffices::getInstance()->updateCache();
            MemberSocials::getInstance()->updateCache();

            //Get updated member data from API routes (member/terms/elections)
            $current = new \UnitedStatesLegislators\CurrentMembers();
            $current->fetchFromApi();
            $historical = new \UnitedStatesLegislators\HistoricalMembers();
            $historical->fetchFromApi();

            $allMembers = array_merge($current->currentMembers,$historical->historicalMembers);

            foreach ($allMembers as $person) {
                $personArr = array_merge($person->id->toArray(), $person->name->toArray(), $person->bio->toArray());
                $personArr = self::setUpdateTimes($personArr);
                $memberRow = new MemberRow($personArr);
                $this->queueInsert($memberRow);
                
                $memberTerms->insertPersonTerms($person);
                $memberElections->insertPersonElections($person);
            }
            $memberTerms->commitInsert();
            $memberElections->commitInsert();
            $this->commitInsert();
            $this->cacheIsValid = true;
        }
        private static $membersObject = null;
        public static function getInstance() {
            if (self::$membersObject == null) 
                self::$membersObject = new Members();
            return self::$membersObject;
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            return MembersQuery::getByBioguideId($bioguideId);
        }

        public static function getByName($firstName = null, $lastName = null) {
            self::enforceCache();
            return MembersQuery::getByName($firstName, $lastName);
        }

        public static function getByGender($gender) {
            self::enforceCache();
            return MembersQuery::getByGender($gender);
        }
    }
}

?>