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
            $members->setEqualityOperator("like");
            $members->setSearchColumns(["first", "last", "isCurrent"]);
            $members->setSearchValues([$firstName, $lastName, $isCurrent]);
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
                $members->addSearchValue("first", "like", $part);
                $members->addSearchValue("last", "like", $part);
                $nameCondition->addCondition(new Condition("first", Comparison::LIKE, $part));
                $nameCondition->addCondition(new Condition("last", Comparison::LIKE, $part));
            }

            $members->setBooleanCondition("OR");
            $members->addSearchConditionGroup($nameCondition);
            
            if (is_bool($isCurrent)) {
                $members->addSearch("isCurrent", Comparison::EQUALS, $isCurrent);
            }

            $members->applyPagination();
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

            if ($state != null) {
                $members->addSearchValue("state", "like", $state);
                $members->addSearch("state", Comparison::LIKE, $state);
            }
            if ($type != null) {
                $members->addSearchValue("type", "like", $type);
                $members->addSearch("type", Comparison::LIKE, $type);
            } 
            if ($party != null) {
                $members->addSearchValue("party", "like", $party);
                $members->addSearch("party", Comparison::LIKE, $party);
            }
            if ($gender != null) {
                $members->addSearchValue("gender", "like", $gender);
                $members->addSearch("gender", Comparison::LIKE, $gender);
            }
            if (is_bool($isCurrent)) {
                $todaysDate = date("Y-m-d");
                $members->addSearchValue("isCurrent", "like", $isCurrent);
                $members->addSearch("isCurrent", Comparison::LIKE, $isCurrent);
                if ($isCurrent === false) {
                    $members->addSearchValue("end", "<=", $todaysDate);
                    $members->addSearch("end", Comparison::LESS_THAN_EQUALS, $todaysDate);
                } else {
                    $members->addSearchValue("start", "<=", $todaysDate);
                    $members->addSearchValue("end", ">=", $todaysDate);
                    $members->addSearch("start", Comparison::LESS_THAN_EQUALS, $todaysDate);
                    $members->addSearch("end", Comparison::GREATER_THAN_EQUALS, $todaysDate);
                }
            }

            $members->setGroupBy(["bioguideId", "type"]);
            $members->setOrderBy(["bioguideId", "start"], false);
            $members->setJoin("members", ["memberterms.bioguideId"], ["members.bioguideId"]);
            $joinGroup = new ConditionGroup();
            $joinGroup->addCondition(new Condition("memberterms.bioguideId", Comparison::EQUALS, "members.bioguideId", true));
            $members->addJoin("members", $joinGroup);

            #var_dump($members->getQueryString());
            #var_dump($members->getOrderedParameters());
            #var_dump($members->getOrderedTypes());

            $members->applyPagination();

            return $members->selectFromDB()->fetchAllAssoc();
        }

        //Update a members image url with the provided url and attribution
        public static function updateMemberImage($bioguideId, $imageUrl, $imageAttribution) {
            $members = new MembersQuery();
            $members->addSearchCondition(new Condition("bioguideId", Comparison::EQUALS, $bioguideId));
            $members->setColumns(["imageUrl", "imageAttribution"]);
            $members->setValues([$imageUrl, $imageAttribution]);
            return $members->updateInDb();
        }
    }
}

?>