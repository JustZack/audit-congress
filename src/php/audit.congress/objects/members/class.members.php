<?php

namespace AuditCongress {
    class Members {
        public 
            $bioguideId,
            $firstName,
            $middleName,
            $lastName,
            $gender,
            $birthYear,
            $deathYear;
        private SQLMembers $sqlObject;

        private static $memberTables = ["members", "membersocials", "memberterms", "memberoffices"]

        private static $cacheIsValid = null;
        private static function cacheIsValid() {
            if (Members::$cacheIsValid != null) return Members::$cacheIsValid;

            $table = new \MySqlConnector\Table("members");
            $topRow = $table->select(["lastUpdate", "nextUpdate"], null, null, 1)->fetchAssoc();
            if ($topRow != null) {
                $next = strtotime($topRow["nextUpdate"]);
                if ($next == false || $next < time()) return false;
                else return true;
                return true;
            } else return false;
        }

        private static function clearCache() {
            //Foreach known table name
            foreach (Members::$memberTables as $name) {
                $table = new \MySqlConnector\Table($name);
                //Drop all rows with a bioguideId (thats all of them)
                if ($table->exists()) $table->delete("bioguideId is not null");
            }
        }

        private static function updateCache() {
            //Clear out all data associated with members
            Members::clearCache();

            //Collect live data
            $current = new \UnitedStatesLegislators\CurrentMembers();
            $current->fetchFromApi();

            $currentOffices = new \UnitedStatesLegislators\CurrentDistrictOffices();
            $currentOffices->fetchFromApi();

            $historical = new \UnitedStatesLegislators\HistoricalMembers();
            $historical->fetchFromApi();

            var_dump($current->currentMembers[0]);
            var_dump($historical->historicalMembers[0]);
            
            //Insert current members and their terms

            //Insert current members offices

            //Insert historical members and their terms

            Members::$cacheIsValid = true;
        }

        public function fetch() {
            //
            if(!Members::cacheIsValid()) Members::updateCache();
            //first, Can we find this member in the DB?
            $result = $this->sqlObject->selectFromDB();
            $rowsFound = $result->fetchAllAssoc();
            //If found no members, the search was likely no good.
            if (count($rowsFound) == 0) {
                //$currentMembers = new \UnitedStatesLegislators\CurrentMembers();
                //$currentMembers->fetchFromApi();
                //$currentMembers->printAsJson();

            } else return $rowsFound;
        }

        /*
            Fetch members by their exact bioguideId
        */
        public static function getByBioguideId($bioguideId) {
            if (empty($bioguideId)) 
                throw new ACException("Members::getByBioguideId() Must provide a bioguideid.");
            
            $members = new Members();
            $members->bioguideId = $bioguideId;
            $members->sqlObject = SQLMembers::getByBioguideId($bioguideId);

            return $members;
        }

        /*
            Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.
        */
        public static function getByName($firstName, $middleName = null, $lastName = null) {
            if (empty($firstName) && empty($middleName) && empty($lastName)) 
                throw new ACException("Members::getByName() Must provide atleast one name.");
                      
            $members = new Members();
            $members->firstName = $firstName;
            $members->middleName = $middleName;
            $members->lastName = $lastName;
            $members->sqlObject = SQLMembers::getByName($firstName, $middleName, $lastName);

            return $members;
        }

        /*
            Fetch members with the given gender (M or F at this time)
        */
        public static function getByGender($gender) {
            if (empty($gender)) throw new ACException("Members::getByGender() Must provide a gender.");

            $members = new Members();
            $members->gender = $gender;
            $members->sqlObject = SQLMembers::getByGender($gender);

            return $members;
        }

        /*
            Fetch members who where born before or on the given birth year
        */
        public static function getBornBy($birthYear) {
            if (empty($birthYear)) throw new ACException("Members::getBornBy() Must provide a birth year.");

            $members = new Members();
            $members->birthYear = $birthYear;
            $members->sqlObject = SQLMembers::getBornBy($birthYear);

            return $members;
        }

        /*
            Fetch members who where born after the given birth year
        */
        public static function getBornAfter($birthYear) {
            if (empty($birthYear)) throw new ACException("Members::getBornAfter() Must provide a birth year.");

            $members = new Members();
            $members->birthYear = $birthYear;
            $members->sqlObject = SQLMembers::getBornAfter($birthYear);

            return $members;
        }

        /*
            Fetch members who died by or on the given death year
        */
        public static function getDeadBy($deathYear) {
            if (empty($deathYear)) throw new ACException("Members::getDeadBy() Must provide a death year.");
        
            $members = new Members();
            $members->deathYear = $deathYear;
            $members->sqlObject = SQLMembers::getDeadBy($deathYear);

            return $members;
        }

        /*
            Fetch members who died after the given death year
        */
        public static function getDeadAfter($deathYear) {
           if (empty($deathYear)) throw new ACException("Members::getDeadAfter() Must provide a death year.");
           
           $members = new Members();
           $members->deathYear = $deathYear;
           $members->sqlObject = SQLMembers::getDeadAfter($deathYear);

           return $members;
        }
    }
}

?>
