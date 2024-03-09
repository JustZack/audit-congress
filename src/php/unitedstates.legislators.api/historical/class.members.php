<?php


namespace UnitedStatesLegislators {
    class HistoricalMembers extends \AuditCongress\ApiObject {
        public
            $uid,

            $historicalMembers,

            $objectArrayField = "historicalMembers",
            $objectArrayType = "UnitedStatesLegislators\Person";
        function __construct() {
            $this->route = "legislators-historical";
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