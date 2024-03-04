<?php


namespace CongressGov {
    class CoSponsoredLegislation extends \AuditCongress\ApiObject {
        public
            $uid,

            $bioguideId,

            $cosponsoredLegislation,

            $apiDataField = "cosponsoredLegislation",
            $objectArrayField = "cosponsoredLegislation",
            $objectArrayType = "CongressGov\Legislation";

        function __construct($bioguideId) {
            $this->bioguideId = $bioguideId;

            $this->route = "member/$this->bioguideId/cosponsored-legislation";
            $this->setUidFromRoute();

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $cosponsoredLegislation = Api::call_bulk($this->route, $this->apiDataField, 100);
            $this->setFromApiAsArray($cosponsoredLegislation, $this->objectArrayField, $this->objectArrayType);
        }
    }
}

?>
