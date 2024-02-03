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

            $this->uid = "$this->actionType.$this->congress.$this->type.$this->number.cosponsors";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("$this->actionType/$this->congress/$this->type/$this->number/cosponsors");
            if (isset($result) && isset($result["cosponsors"])) {
                $texts = $result["cosponsors"];
                $this->setFromApiAsArray($texts, "CongressGov\Cosponsor", "cosponsors");
                $this->type = strtolower($this->type);
            } else throw new \Exception("CongressGov.Api => $this->actionType/$this->congress/$this->type/$this->number/cosponsors returned null value");
        }
    }

    class Cosponsor {
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
                unset($cosponsorObject["url"]);
                foreach ($cosponsorObject as $key=>$value) $this->{$key} = $value;
            }
    }
}

?>
