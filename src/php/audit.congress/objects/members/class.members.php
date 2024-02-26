<?php

namespace AuditCongress {

    class Members {

        /*
            Fetch members by their exact bioguideId
        */
        public static function getByBioguideId($bioguideId) {
            if (empty($bioguideId)) 
                throw new ACException("Members::getByBioguideId() Must provide a bioguideid.");
        }

        /*
            Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.
        */
        public static function getByName($firstName, $middleName = null, $lastName = null) {
            if (empty($firstName) && empty($middleName) && empty($lastName)) 
                throw new ACException("Members::getByName() Must provide atleast one name.");
            
        }

        /*
            Fetch members with the given gender (M or F at this time)
        */
        public static function getByGender($gender) {
            if (empty($gender)) throw new ACException("Members::getByGender() Must provide a gender.");
        }

        /*
            Fetch members who where born before or on the given birth year
        */
        public static function getBornBy($birthYear) {
            if (empty($birthYear)) throw new ACException("Members::getBornBy() Must provide a birth year.");
        }

        /*
            Fetch members who where born after the given birth year
        */
        public static function getBornAfter($birthYear) {
            if (empty($birthYear)) throw new ACException("Members::getBornAfter() Must provide a birth year.");
        }

        /*
            Fetch members who died by or on the given death year
        */
        public static function getDeadBy($deathYear) {
            if (empty($deathYear)) throw new ACException("Members::getDeadBy() Must provide a death year.");
        }

        /*
            Fetch members who died after the given death year
        */
        public static function getDeadAfter($deathYear) {
           if (empty($deathYear)) throw new ACException("Members::getDeadAfter() Must provide a death year.");
           
        }
    }
}

?>