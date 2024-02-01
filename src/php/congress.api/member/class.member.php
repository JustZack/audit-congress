<?php


namespace CongressGov {
    require_once CONGRESSGOV_FOLDER."\\congress.api.php";
    require_once AUDITCONGRESS_FOLDER."\\abstract.api.object.php";
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
        }

        function fetchFromApi() {
            $result = Api::call("member/$this->id.json");
            if (isset($result) && isset($result["member"])) {
                $mem = $result["member"];
                $this->setFromApi($mem);
                $this->getUid();
            } else throw new \Exception("CongressGov.Api => member/$this->id.json returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "member.$this->id";
            return $this->uid;
        }
    }
}

?>