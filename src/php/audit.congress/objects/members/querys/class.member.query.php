<?php 

namespace AuditCongress {

    use \MySqlConnector\SqlObject;

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
}

?>