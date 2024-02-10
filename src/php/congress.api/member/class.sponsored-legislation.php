<?php


namespace CongressGov {
    class SponsoredLegislation extends \AuditCongress\ApiObject {
        public
            $uid,

            $bioguideId,

            $sponsoredLegislation,

            $apiDataField = "sponsoredLegislation",
            $objectArrayField = "sponsoredLegislation",
            $objectArrayType = "CongressGov\Legislation";

        function __construct($bioguideId) {
            $this->bioguideId = $bioguideId;

            $this->route = "member/$this->bioguideId/sponsored-legislation";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $sponsoredLegislation = Api::call_bulk($this->route, $this->apiDataField, 100);
            $this->setFromApiAsArray($sponsoredLegislation, $this->objectArrayField, $this->objectArrayType);
        }
    }


}

?>
