<?php


namespace UnitedStatesLegislators {
    class HistoricalCommittees extends \AuditCongress\ApiObject {
        public
            $uid,

            $historicalCommittees,

            $objectArrayField = "historicalCommittees",
            $objectArrayType = "UnitedStatesLegislators\Committee";
        function __construct() {
            $this->route = "committees-historical";
            $this->setUidFromRoute();
            $this->route .= ".json";

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $current = Api::call($this->route);
            $this->setFromApiAsArray($current, $this->objectArrayField, $this->objectArrayType);
        }
    }
}

?>