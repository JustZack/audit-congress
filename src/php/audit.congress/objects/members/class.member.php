<?php 

namespace AuditCongress {

    class Member {
        public $fetchType, 
               $bioguideId,
               $firstName,
               $lastName;
            public SQLMembers $sqlObject;

        public static function getByBioguideId($bioguideId) {
            $member = new Member();
            $member->bioguideId = $bioguideId;
            $member->sqlObject = SQLMembers::getByBioguideId($bioguideId);
            return $member;
        }

        public static function getByName($firstName, $middleName, $lastName) {
            $member = new Member();
            $member->firstName = $firstName;
            $member->lastName = $lastName;
            $member->sqlObject = SQLMembers::getByName($firstName, $middleName, $lastName);
            return $member;
        }

        public function fetch() {
            //first, Can we find this member in the DB?
            $result = $this->sqlObject->selectFromDB();
            $rowsFound = $result->fetchAllAssoc();
            //Found no members
            if (count($rowsFound) == 0) {
                //$currentMembers = new \UnitedStatesLegislators\CurrentMembers();
                //$currentMembers->fetchFromApi();
                //$currentMembers->printAsJson();

            } else return $rowsFound;
        }

        public static function FetchCurrentMembers() {}
        public static function UpdateCurrentMembers() {}
    }
}

?>