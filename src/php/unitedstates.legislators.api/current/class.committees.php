<?php


namespace UnitedStatesLegislators {
    class CurrentCommittees extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentCommittees,

            $objectArrayField = "currentCommittees";
            //$objectArrayType = "UnitedStatesLegislators\CurrentMember";
        function __construct() {
            $this->route = "committees-current";
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