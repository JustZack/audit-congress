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
            $updateDate;

        function __construct($memberId) {
            $this->id = $memberId;

            $this->route = "member/$this->id";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call($this->route);
            if (isset($result) && isset($result["member"])) {
                $mem = $result["member"];
                $this->setFromApi($mem);
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
        }
    }
}

?>