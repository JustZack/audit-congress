<?php 

namespace AuditCongress {

    use UnitedStatesLegislators\HistoricalMembers;
    use UnitedStatesLegislators\CurrentMembers;
    use \MySqlConnector\SqlRow;
    use \MySqlConnector\SqlObject;

    class MemberRow extends SqlRow {
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
            $isCurrent,

            $lastUpdate,
            $nextUpdate;
    
        public function getColumns() {
            return ["bioguideId","thomasId","lisId","govTrackId",
            "openSecretsId","voteSmartId","cspanId","mapLightId","icpsrId",
            "wikidata","google_entity_id","official_full","first","last",
            "gender","birthday","imageUrl","imageAttribution","isCurrent",
            "lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguide,$this->thomas,$this->lis,$this->govtrack,
            $this->opensecrets,$this->votesmart,$this->cspan,$this->maplight,
            $this->icpsr,$this->wikidata,$this->google_entity_id,
            $this->official_full,$this->first,$this->last,
            $this->gender,$this->birthday,$this->imageUrl,$this->imageAttribution,
            $this->isCurrent, $this->lastUpdate,$this->nextUpdate];
        }
    }
    class MembersQuery extends SqlObject {
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

    class Members extends MemberTable {
        private ?MemberTerms $termsInstance = null;
        private ?MemberElections $electionsInstance = null;

        private function __construct() {
            parent::__construct("Members");
            $this->termsInstance = MemberTerms::getInstance();
            $this->electionsInstance = MemberElections::getInstance();
        }

        //Update the members cache
        //Note that this updates ALL member tables
        public function updateCache() {
            var_dump("Update cache for: ".$this->name);
            var_dump("Update cache for: MemberTerms");
            var_dump("Update cache for: MemberElections");
            //Force update cache for Offices and Socials
            //These tables contain information from OTHER api routes
            MemberOffices::getInstance()->updateCache();
            MemberSocials::getInstance()->updateCache();
            //Clear rows for elections, terms, and member bios
            //These tables contain information from the members route only
            $this->termsInstance->clearRows();
            $this->electionsInstance->clearRows();
            $this->clearRows();
            //Get updated member data from API routes (member/terms/elections)
            $current = new CurrentMembers();
            $historical = new HistoricalMembers();
            //Queue up inserting members, their terms, and elections
            $this->insertMembers($current->currentMembers, true);
            $this->insertMembers($historical->historicalMembers, false);
            //Commit the insert for all tables
            $this->termsInstance->commitInsert();
            $this->electionsInstance->commitInsert();
            $this->commitInsert();
            //Aftering updating member tables, the cache is valid
            $this->cacheIsValid = true;
        }
        //Insert current or historical member rows
        private function insertMembers($members, $isCurrent) {
            foreach ($members as $person) {
                $personArr = array_merge($person->id->toArray(), $person->name->toArray(), $person->bio->toArray());
                $personArr = self::setUpdateTimes($personArr);
                $personArr["isCurrent"] = $isCurrent;
                $memberRow = new MemberRow($personArr);
                
                $this->termsInstance->insertPersonTerms($person);
                $this->electionsInstance->insertPersonElections($person);
                $this->queueInsert($memberRow);
            }
        }

        private static $membersObject = null;
        public static function getInstance() {
            if (self::$membersObject == null) 
                self::$membersObject = new Members();
            return self::$membersObject;
        }

        //Check every member relevent table for cache validity
        //Update members cache if needed
        public static function enforceCache() {
            $members = Members::getInstance();
            $terms = MemberElections::getInstance();
            $elections = MemberTerms::getInstance();
            $socials = MemberSocials::getInstance();
            $offices = MemberOffices::getInstance();
            $allCachesValid = $members->cacheIsValid() 
                            && $terms->cacheIsValid() && $elections->cacheIsValid()
                            && $socials->cacheIsValid() && $offices->cacheIsValid();

            if (!$allCachesValid) $members->updateCache();
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