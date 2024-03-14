<?php 

namespace AuditCongress {

    use UnitedStatesLegislators\HistoricalMembers;
    use UnitedStatesLegislators\CurrentMembers;

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
                //if (!isset($row["imageUrl"])) break;
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
            return $rows;
        }

        public static function getBioguideToThomasIdMapping() {
            self::enforceCache();
            $members = MembersQuery::getBioguideToThomasIdMapping();
            $mapping = array();
            foreach ($members as $member) $mapping[$member["thomasId"]] = $member["bioguideId"];
            return $mapping;
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

        public static function getByState($state = null, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByState($state, $isCurrent);
            return self::parseResult($members);
        }

        public static function getSenators($state = null, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getSenators($state, $isCurrent);
            return self::parseResult($members);
        }

        public static function getRepresentatives($state = null, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getRepresentatives($state, $isCurrent);
            return self::parseResult($members);
        }
    }
}

?>