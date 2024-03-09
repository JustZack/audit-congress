<?php


namespace UnitedStatesLegislators {
    class CurrentMembers extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentMembers,

            $objectArrayField = "currentMembers",
            $objectArrayType = "UnitedStatesLegislators\Person";
        function __construct() {
            $this->route = "legislators-current";
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