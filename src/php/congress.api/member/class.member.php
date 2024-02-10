<?php


namespace CongressGov {
    class Member extends \AuditCongress\ApiObject {
        public
            $uid,

            $id,
            $bioguideId,
            $birthYear,
            $cosponsoredLegislation,
            $depiction,
            $directOrderName,
            $district,
            $firstName,
            $invertedOrderName,
            $middleName,
            $lastName,
            $partyHistory,
            $sponsoredLegislation,
            $state,
            $terms,
            $updateDate,

            $apiDataField = "member";

        function __construct($memberId) {
            $this->id = $memberId;

            $this->route = "member/$this->id";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $member = Api::call($this->route, $this->apiDataField);
            $this->setFromApi($member);
        }


        function getOption($subRoute) {
            $id = $this->bioguideId;
            switch ($subRoute) {
                case "sponsored-legislation": $obj = new SponsoredLegislation($id); break;
                case "cosponsored-legislation": $obj = new CoSponsoredLegislation($id); break;
                default: break;
            }

            return $obj;
        }
        static function getOptionList() {
            return ["sponsored-legislation", "cosponsored-legislation"];
        }
    }
}

?>