<?php 

namespace AuditCongress {

    class MembersQuery extends AuditCongressQuery {
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

        private static function getCongressPeopleByState($state = null, $type = null, $isCurrent = null) {
            $members = new MemberTermsQuery();
            $members->setSelectColumns(["members.*"]);
            $searchColumns = $searchValues = $searchOperators = [];

            if (is_bool($isCurrent)) {
                $todaysDate = date("Y-m-d");

                if ($isCurrent === false) {
                    $searchColumns = ["state", "type", "isCurrent", "end"];
                    $searchValues = [$state, $type, $isCurrent, $todaysDate];
                    $searchOperators = ["like", "like", "like", "<="];
                } else {
                    $searchColumns = ["state", "type", "isCurrent", "end", "start"];
                    $searchValues = [$state, $type, $isCurrent, $todaysDate, $todaysDate];
                    $searchOperators = ["like", "like", "like", ">=", "<="];
                }
            }

            $members->setSearchColumns($searchColumns);
            $members->setSearchValues($searchValues);
            $members->setEqualityOperators($searchOperators);

            $members->setJoin("members", ["memberterms.bioguideId"], ["members.bioguideId"]);

            $members->setGroupBy(["memberterms.bioguideId", "type"]);
            $members->setOrderBy(["bioguideId", "start"], false);
            
            return $members->selectFromDB()->fetchAllAssoc();
        }


        public static function getByState($state, $isCurrent = null) {
            return self::getCongressPeopleByState($state, null, $isCurrent);
        }

        public static function getSenators($state = null, $isCurrent = null) {
            return self::getCongressPeopleByState($state, "sen", $isCurrent);
        }

        public static function getRepresentatives($state = null, $isCurrent = null) {
            return self::getCongressPeopleByState($state, "rep", $isCurrent);
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