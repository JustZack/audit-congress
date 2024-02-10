<?php


namespace UnitedStatesLegislators {
    class HistoricalCommittees extends \AuditCongress\ApiObject {
        public
            $uid,

            $historicalCommittees,

            $objectArrayField = "historicalCommittees";
            //$objectArrayType = "UnitedStatesLegislators\CurrentMember";
        function __construct() {
            $this->route = "committees-historical";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $current = Api::call($this->route);
            $this->setFromApiAsArray($current, $this->objectArrayField);
        }
    }
}

?>