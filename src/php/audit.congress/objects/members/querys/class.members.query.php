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

        //Fetch members by state with some parameters, where each value needs to be satisifed in to find a result
        //Note: This requires a join on the terms table, so its somewhat expensive to run.
        public static function getByFilter($state = null, $type = null, $party = null, $gender = null, $isCurrent = null) {
            $members = new MemberTermsQuery();
            $members->setSelectColumns(["members.*", "memberterms.state", "memberterms.start", "memberterms.end", "memberterms.type", "memberterms.party"]);

            if ($state != null) $members->addSearchValue("state", "like", $state);
            if ($type != null) $members->addSearchValue("type", "like", $type);
            if ($party != null) $members->addSearchValue("party", "like", $party);
            if ($gender != null) $members->addSearchValue("gender", "like", $gender);
            if (is_bool($isCurrent)) {
                $todaysDate = date("Y-m-d");
                $members->addSearchValue("isCurrent", "like", $isCurrent);
                if ($isCurrent === false) {
                    $members->addSearchValue("end", "<=", $todaysDate);
                } else {
                    $members->addSearchValue("start", "<=", $todaysDate);
                    $members->addSearchValue("end", ">=", $todaysDate);
                }
            }

            $members->setGroupBy(["memberterms.bioguideId", "memberterms.type"]);
            $members->setOrderBy(["bioguideId", "start"], false);
            $members->setJoin("members", ["memberterms.bioguideId"], ["members.bioguideId"]);

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