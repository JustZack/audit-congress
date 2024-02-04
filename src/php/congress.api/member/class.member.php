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
    }
}

?>