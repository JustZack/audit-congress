<?php 

namespace AuditCongress {

    use MySqlConnector\Comparison;
    use MySqlConnector\Logical;
    use MySqlConnector\Condition;
    use MySqlConnector\ConditionGroup;

    class MembersQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery;
        
        public function __construct() {
            parent::__construct("Members");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["bioguideId"], false);
        }

        public static function getBioguideToThomasIdMapping() {
            $members = new MembersQuery();
            $members->setSelectColumns(["bioguideId", "thomasId"]);
            return $members->selectFromDB()->fetchAllAssoc();
        }

        /*Fetch members whose names contain the given first, middle, or last name
                Must provide atleast one of the names.*/
        public static function getByName($firstName = null, $lastName = null, $isCurrent = null) {
            $members = new MembersQuery();

            if ($firstName != null) $members->addSearch("first", Comparison::LIKE, $firstName);
            if ($lastName != null) $members->addSearch("last", Comparison::LIKE, $lastName);
            if ($isCurrent != null) $members->addSearch("isCurrent", Comparison::EQUALS, $isCurrent);
            
            $members->applyPagination();
            return $members->selectFromDB()->fetchAllAssoc();
        }
        
        /*Fetch members whose names contain the given first, middle, or last name
        Must provide atleast one of the names.*/
        public static function getByAnyName($name, $isCurrent = null) {
            $nameParts = preg_split("/[\s\.\+-,]/", $name);
            //$searchColumns = ["isCurrent"]; $searchValues = [$isCurrent];
            
            $members = new MembersQuery();
            $nameCondition = new ConditionGroup(Logical::OR);
            foreach ($nameParts as $part) {
                $nameCondition->addCondition(new Condition("first", Comparison::LIKE, $part));
                $nameCondition->addCondition(new Condition("last", Comparison::LIKE, $part));
            }

            $members->addSearchConditionGroup($nameCondition);
            
            if (is_bool($isCurrent)) {
                $members->addSearch("isCurrent", Comparison::EQUALS, $isCurrent, Logical::AND);
            }

            $members->applyPagination();
            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Fetch members by state with some parameters, where each value needs to be satisifed in to find a result
        //Note: This requires a join on the terms table, so its somewhat expensive to run.
        public static function getByFilter($state = null, $type = null, $party = null, $gender = null, $isCurrent = null) {
            $members = new MemberTermsQuery();
            $members->setSelectColumns(["members.*", "memberterms.state", "memberterms.start", "memberterms.end", "memberterms.type", "memberterms.party"]);

            if ($state != null) $members->addSearch("state", Comparison::LIKE, $state);
            if ($type != null) $members->addSearch("type", Comparison::LIKE, $type);
            if ($party != null) $members->addSearch("party", Comparison::LIKE, $party);
            if ($gender != null) $members->addSearch("gender", Comparison::LIKE, $gender);

            if (is_bool($isCurrent)) {
                $todaysDate = date("Y-m-d");
                $members->addSearch("isCurrent", Comparison::EQUALS, $isCurrent);
                if ($isCurrent === false) {
                    $members->addSearch("end", Comparison::LESS_THAN_EQUALS, $todaysDate);
                } else {
                    $members->addSearch("start", Comparison::LESS_THAN_EQUALS, $todaysDate);
                    $members->addSearch("end", Comparison::GREATER_THAN_EQUALS, $todaysDate);
                }
            }

            $members->setGroupBy(["bioguideId", "type"]);
            $members->setOrderBy(["bioguideId", "start"], false);

            $joinGroup = new ConditionGroup();
            $joinGroup->addCondition(new Condition("memberterms.bioguideId", Comparison::EQUALS, "members.bioguideId", true));
            $members->addJoin("Members", $joinGroup);

            $members->applyPagination();
            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Update a members image url with the provided url and attribution
        public static function updateMemberImage($bioguideId, $imageUrl, $imageAttribution) {
            $members = new MembersQuery();
            $members->addSearch("bioguideId", Comparison::EQUALS, $bioguideId);
            $members->setColumns(["imageUrl", "imageAttribution"]);
            $members->setValues([$imageUrl, $imageAttribution]);
            return $members->updateInDb();
        }
    }
}

?>