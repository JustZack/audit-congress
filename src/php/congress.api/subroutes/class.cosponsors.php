<?php


namespace CongressGov {
    class Cosponsors extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,
            
            $actionType,

            $cosponsors,

            $apiDataField = "cosponsors",
            $objectArrayField = "cosponsors",
            $objectArrayType = "CongressGov\Cosponsor";

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/cosponsors";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $cosponsors = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($cosponsors, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
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
