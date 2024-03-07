<?php 

namespace AuditCongress {

    use UnitedStatesLegislators\HistoricalMembers;
    use UnitedStatesLegislators\CurrentMembers;
    use \MySqlConnector\SqlRow;
    use \MySqlConnector\SqlObject;

    class MemberRow extends SqlRow {
        public
            $bioguideId,
            $thomasId,
            $lisId,
            $govTrackId,
            $openSecretsId,
            $voteSmartId,
            $cspanId,
            $mapLightId,
            $icpsrId,
            $wikidataId,
            $googleEntityId,

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
            "wikidataId","googleEntityId","official_full","first","last",
            "gender","birthday","imageUrl","imageAttribution","isCurrent",
            "lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguideId,$this->thomasId,$this->lisId,$this->govTrackId,
            $this->openSecretsId,$this->voteSmartId,$this->cspanId,$this->mapLightId,
            $this->icpsrId,$this->wikidataId,$this->googleEntityId,
            $this->official_full,$this->first,$this->last,
            $this->gender,$this->birthday,$this->imageUrl,$this->imageAttribution,
            $this->isCurrent, $this->lastUpdate,$this->nextUpdate];
        }
    }
    class MembersQuery extends SqlObject {
        public function __construct() {
            parent::__construct("Members");
        }

        public static function getByBioguideId($bioguideId, $isCurrent = null) {
            $members = new MembersQuery();
            $members->setSearchColumns(["bioguideId", "isCurrent"]);
            $members->setSearchValues([$bioguideId, $isCurrent]);
            return $members->selectFromDB()->fetchAllAssoc();
        }

        /*Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.*/
        public static function getByName($firstName = null, $lastName = null, $isCurrent = null) {
            $members = new MembersQuery();
            $members->setEqualityOperator("like");
            $members->setSearchColumns(["first", "last", "isCurrent"]);
            $members->setSearchValues([$firstName, $lastName, $isCurrent]);
            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Fetch members with the given gender (M or F at this time)
        public static function getByGender($gender, $isCurrent = null) {
            $members = new MembersQuery();
            $members->setSearchColumns(["gender", "isCurrent"]);
            $members->setSearchValues([$gender, $isCurrent]);
            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Update a members image url with the provided url and attribution
        public static function updateMemberImage($bioguideId, $imageUrl, $imageAttribution) {
            $members = new MembersQuery();
            $members->setSearchColumns(["bioguideId"]);
            $members->setSearchValues([$bioguideId]);
            $members->setColumns(["imageUrl", "imageAttribution"]);
            $members->setValues([$imageUrl, $imageAttribution]);
            return $members->updateInDb();
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
                $this->termsInstance->insertPersonTerms($person);
                $this->electionsInstance->insertPersonElections($person);

                $member = self::apiPersonToRow($person, $isCurrent);
                $member = new MemberRow($member);
                $this->queueInsert($member);
            }
        }

        private static function apiPersonToRow($person, $isCurrent) {
            $rowArray["bioguideId"] = $person->id->bioguide;
            $rowArray["thomasId"] = $person->id->thomas;
            $rowArray["lisId"] = $person->id->lis;
            $rowArray["govTrackId"] = $person->id->govtrack;
            $rowArray["openSecretsId"] = $person->id->opensecrets;
            $rowArray["voteSmartId"] = $person->id->votesmart;
            $rowArray["cspanId"] = $person->id->cspan;
            $rowArray["mapLightId"] = $person->id->maplight;
            $rowArray["icpsrId"] = $person->id->icpsr;
            $rowArray["wikidataId"] = $person->id->wikidata;
            $rowArray["googleEntityId"] = $person->id->google_entity_id;
            $rowArray = array_merge($rowArray, $person->name->toArray(), $person->bio->toArray());
            $rowArray["isCurrent"] = $isCurrent;
            $rowArray = self::setUpdateTimes($rowArray);
            return $rowArray;
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

        //Set the given members image if available (false if not)
        public static function getMemberImage($bioguideId) {
            $imageUrl = $imageAttribution = 'false';
            $depiction = false;
            
            try {
                $congressMember = new \CongressGov\Member($bioguideId);
                $depiction = $congressMember->depiction;
            } catch (ApiException $e) { }

            if (is_array($depiction)) 
                list("imageUrl"=>$imageUrl, "attribution"=>$imageAttribution) = $depiction;
            return array("imageUrl" => $imageUrl, "imageAttribution" => $imageAttribution);
        }
        //Make sure the given array of members have image urls set
        public static function ensureMembersHaveImage($rows) {
            $members = array();
            foreach ($rows as $row) {
                if ($row["imageUrl"] == '') {
                    $bioguideId = $row["bioguideId"];
                    list("imageUrl"=>$imgUrl, "imageAttribution"=>$imgAttr) = self::getMemberImage($bioguideId);
                    $row["imageUrl"] = $imgUrl; 
                    $row["imageAttribution"] = $imgAttr;
                    MembersQuery::updateMemberImage($bioguideId, $imgUrl, $imgAttr);
                }
                array_push($members, $row);
            }
            return $members;
        }

        protected static function parseResult($rows) {
            $rows = self::ensureMembersHaveImage($rows);
            return MemberResult::rowsToObjects($rows);
        }

        public static function getByBioguideId($bioguideId, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByBioguideId($bioguideId, $isCurrent);
            return self::returnFirst(self::parseResult($members));
        }

        public static function getByName($firstName = null, $lastName = null, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByName($firstName, $lastName, $isCurrent);
            return self::parseResult($members);
        }

        public static function getByGender($gender, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByGender($gender, $isCurrent);
            return self::parseResult($members);
        }
    }

    class MemberResult {
        public MemberRow $member;
        public ?MemberTermRow $firstTerm;
        public ?MemberTermRow $lastTerm;
        public ?MemberSocialsRow $socials;

        public static function rowsToObjects($rows) {
            $objects = array();
            foreach ($rows as $row) array_push($objects, new MemberResult($row));
            return $objects;
        }

        public function __construct($memberArray) {
            $this->member = new MemberRow($memberArray);
            $bioguideId = $this->member->bioguideId;
            $this->firstTerm = MemberTerms::getFirstByBioguideId($bioguideId);
            $this->lastTerm = MemberTerms::getLastByBioguideId($bioguideId);
            $this->socials = MemberSocials::getByBioguideId($bioguideId);
        }
    }
}

?>