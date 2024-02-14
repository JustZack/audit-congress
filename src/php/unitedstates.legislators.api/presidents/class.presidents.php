<?php


namespace UnitedStatesLegislators {
    class Presidents extends \AuditCongress\ApiObject {
        public
            $uid,

            $presidents,

            $objectArrayField = "presidents",
            $objectArrayType = "UnitedStatesLegislators\Person";
        function __construct() {
            $this->route = "executive";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $current = Api::call($this->route);
            $this->setFromApiAsArray($current, $this->objectArrayField, $this->objectArrayType);
        }
    }
}

?>