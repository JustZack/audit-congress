<?php


namespace UnitedStatesLegislators {
    class CurrentMembers extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentMembers,

            $objectArrayField = "currentMembers";
            //$objectArrayType = "UnitedStatesLegislators\CurrentMember";
        function __construct() {
            $this->route = "legislators-current";
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