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

            $this->uid = "member.$this->id";
        }

        function fetchFromApi() {
            $result = Api::call("member/$this->id");
            if (isset($result) && isset($result["member"])) {
                $mem = $result["member"];
                $this->setFromApi($mem);
            } else throw new \Exception("CongressGov.Api => member/$this->id.json returned null value");
        }
    }
}

?>