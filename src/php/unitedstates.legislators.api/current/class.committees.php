<?php


namespace UnitedStatesLegislators {
    class CurrentCommittees extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentCommittees,

            $objectArrayField = "currentCommittees",
            $objectArrayType = "UnitedStatesLegislators\Committee";
        function __construct() {
            $this->route = "committees-current";
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