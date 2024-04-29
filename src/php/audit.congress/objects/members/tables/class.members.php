<?php 

namespace AuditCongress {

    class Members extends MemberTable {

        private function __construct() {
            parent::__construct("Members");
        }

        private static $membersObject = null;
        public static function getInstance() {
            if (self::$membersObject == null) 
                self::$membersObject = new Members();
            return self::$membersObject;
        }

        //Set the given members image if available (false if not)
        public static function getMemberImage($bioguideId) {
            $imageUrl = $imageAttribution = 'false';
            $depiction = false;
            
            try {
                $congressMember = new \CongressGov\Member($bioguideId);
                $depiction = $congressMember->depiction;
            } catch (ApiException $e) { }

            if (is_array($depiction)) {
                $imageUrl = in_array("imageUrl", $depiction) ? $depiction["imageUrl"] : 'false';
                $imageAttribution = in_array("attribution", $depiction) ? $depiction["attribution"] : 'false';
            }

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
            foreach ($members as $member) 
                if (strlen($member["thomasId"]) > 0 && strlen($member["bioguideId"]) > 0)
                    $mapping[$member["thomasId"]] = $member["bioguideId"];
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

        public static function getByAnyName($name, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByAnyName($name, $isCurrent);
            return self::parseResult($members);
        }

        public static function getByFilter($state = null, $type = null, $party = null, $gender = null, $isCurrent = null) {
            self::enforceCache();
            $members = MembersQuery::getByFilter($state, $type, $party, $gender, $isCurrent);
            return self::parseResult($members);
        }

        public static function getByParty($state = null, $party = null, $isCurrent = null) {
            self::enforceCache();
            $members = self::getByFilter($state, null, $party, null, $isCurrent);
            return self::parseResult($members);
        }

        public static function getByState($state = null, $type = null, $isCurrent = null) {
            self::enforceCache();
            $members = self::getByFilter($state, $type, null, null, $isCurrent);
            return self::parseResult($members);
        }

        public static function getSenators($state, $isCurrent = null) {
            self::enforceCache();
            $members = self::getByState($state, "sen", $isCurrent);
            return self::parseResult($members);
        }

        public static function getRepresentatives($state, $isCurrent = null) {
            self::enforceCache();
            $members = self::getByState($state, "rep", $isCurrent);
            return self::parseResult($members);
        }
    }
}

?>