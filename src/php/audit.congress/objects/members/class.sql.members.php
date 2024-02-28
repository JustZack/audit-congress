<?php

namespace AuditCongress {
    class SQLMembers extends \MySqlConnector\SqlObject {

        public function __construct($equalityOperator = "=", $booleanCondition = "AND") {
            parent::__construct("members", $equalityOperator, $booleanCondition);
        }

        public static function getByBioguideId($bioguideID) {
            $obj = new SQLMembers();
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["bioguideId"]);
            $obj->setValues([$bioguideID]);
            return $obj;
        }

        public static function getByName($firstName, $middleName = null, $lastName = null) {
            $obj = new SQLMembers("like", "OR");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["FirstName", "MiddleName", "LastName"]);
            $obj->setValues([$firstName, $middleName, $lastName]);
            return $obj;
        }

        public static function getByGender($gender) {
            $obj = new SQLMembers("like");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["Gender"]);
            $obj->setValues([$gender]);
            return $obj;
        }

        public static function getBornBy($birthYear) {
            $obj = new SQLMembers("<=");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["BirthYear"]);
            $obj->setValues([$birthYear]);
            return $obj;
        }

        public static function getBornAfter($birthYear) {
            $obj = new SQLMembers(">");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["BirthYear"]);
            $obj->setValues([$birthYear]);
            return $obj;
        }

        public static function getDeadBy($deathYear) {
            $obj = new SQLMembers("<=");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["DeathYear"]);
            $obj->setValues([$deathYear]);
            return $obj;
        }

        public static function getDeadAfter($deathYear) {
            $obj = new SQLMembers(">");
            $obj->setSelectColumns(["*"]);
            $obj->setColumns(["DeathYear"]);
            $obj->setValues([$deathYear]);
            return $obj;
        }
    }
}

?>