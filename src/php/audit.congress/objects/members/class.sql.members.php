<?php

namespace AuditCongress {
    class SQLMembers extends \MySqlConnector\SqlObject {

        public function __construct($booleanCondition = "AND", $useLike = false) {
            parent::__construct("members", $booleanCondition, $useLike);
        }

        public static function getByBioguideId($bioguideID) {
            $obj = new SQLMembers();
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["bioguideId"]);
            $obj->setValues([$bioguideID]);
            return $obj;
        }

        public static function getByName($firstName, $middleName = null, $lastName = null) {
            $obj = new SQLMembers("OR", true);
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["FirstName", "MiddleName", "LastName"]);
            $obj->setValues([$firstName, $middleName, $lastName]);
            return $obj;
        }

        public static function getByGender($gender) {
            
        }

        public static function getBornBy($birthYear) {
           
        }

        public static function getBornAfter($birthYear) {
            
        }

        public static function getDeadBy($deathYear) {
            
        }

        public static function getDeadAfter($deathYear) {
           
        }
    }
}

?>