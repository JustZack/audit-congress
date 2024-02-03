<?php


namespace CongressGov {
    class Cosponsors extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,
            
            $actionType,

            $cosponsors;

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/cosponsors";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call_bulk($this->route);
            if (isset($result) && isset($result["cosponsors"])) {
                $cosponsors = $result["cosponsors"];
                $this->setFromApiAsArray($cosponsors, "cosponsors", "CongressGov\Cosponsor");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
        }
    }

    class Cosponsor extends \AuditCongress\ApiChildObject {
        public
            $bioguideId,
            $district,

            $firstName,
            $middleName,
            $lastName,
            $fullName,

            $party,
            $state,
            $sponsorshipDate,
            $isOriginalCosponsor;
            
            function __construct($cosponsorObject) {
                $this->setFieldsFromObject($cosponsorObject);
                $this->unsetField("url");
            }
    }
}

?>
