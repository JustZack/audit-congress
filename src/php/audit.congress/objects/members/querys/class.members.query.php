<?php 

namespace AuditCongress {

    class MembersQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("Members");
        }

        public static function getBioguideToThomasIdMapping() {
            $members = new MembersQuery();
            $members->setSelectColumns(["bioguideId", "thomasId"]);
            return $members->selectFromDB()->fetchAllAssoc();
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
        
        /*Fetch members whose names contain the given first, middle, or last name
        Must provide atleast one of the names.*/
        public static function getByAnyName($name, $isCurrent = null) {
            $nameParts = preg_split("/[\s\.\+-,]/", $name);
            //$searchColumns = ["isCurrent"]; $searchValues = [$isCurrent];
            $searchColumns = []; $searchValues = [];
            
            foreach ($nameParts as $part) {
                array_push($searchColumns, "first", "last");
                array_push($searchValues, $part, $part);
            }

            $members = new MembersQuery();
            $members->setEqualityOperator("like");
            $members->setBooleanCondition("OR");
            $members->setSearchColumns($searchColumns);
            $members->setSearchValues($searchValues);
            $result = $members->selectFromDB()->fetchAllAssoc();
            
            if (is_bool($isCurrent)) {
                $isCurrent = $isCurrent?"1":"0"; $toKeep = [];
                foreach ($result as $mem) if ($mem["isCurrent"] == $isCurrent) array_push($toKeep, $mem);
                $result = $toKeep;
            }
            return $result;
        }

        //Fetch members with the given gender (M or F at this time)
        public static function getByGender($gender, $isCurrent = null) {
            $members = new MembersQuery();
            $members->setSearchColumns(["gender", "isCurrent"]);
            $members->setSearchValues([$gender, $isCurrent]);
            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Fetch members by state with some parameters. 
        //Note: This requires a join on the terms table, so its somewhat expensive to run.
        private static function getCongressPeopleByState($state, $type = null, $gender = null, $isCurrent = null) {
            $members = new MemberTermsQuery();
            $members->setSelectColumns(["members.*"]);
            $searchColumns = $searchOperators = $searchValues = [];

            if (is_bool($isCurrent)) {
                $todaysDate = date("Y-m-d");
                if ($isCurrent === false) {
                    array_push($searchColumns, "state", "type", "isCurrent", "end");
                    array_push($searchOperators, "like", "like", "like", "<=");
                    array_push($searchValues, $state, $type, $isCurrent, $todaysDate);
                } else {
                    array_push($searchColumns, "state", "type", "isCurrent", "end", "start");
                    array_push($searchOperators, "like", "like", "like", ">=", "<=");
                    array_push($searchValues, $state, $type, $isCurrent, $todaysDate, $todaysDate);
                }
            }

            if ($gender != null) {
                array_push($searchColumns, "gender");
                array_push($searchOperators, "=");
                array_push($searchValues, $gender);
            }

            $members->setSearchColumns($searchColumns);
            $members->setSearchValues($searchValues);
            $members->setEqualityOperators($searchOperators);

            $members->setJoin("members", ["memberterms.bioguideId"], ["members.bioguideId"]);

            $members->setGroupBy(["memberterms.bioguideId", "type"]);
            $members->setOrderBy(["bioguideId", "start"], false);
            
            return $members->selectFromDB()->fetchAllAssoc();
        }

        public static function getByState($state, $type = null, $gender = null, $isCurrent = null) {
            return self::getCongressPeopleByState($state, $type, $gender, $isCurrent);
        }

        public static function getSenators($state, $isCurrent = null) {
            return self::getCongressPeopleByState($state, "sen", null, $isCurrent);
        }

        public static function getRepresentatives($state, $isCurrent = null) {
            return self::getCongressPeopleByState($state, "rep", null, $isCurrent);
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
}

?>