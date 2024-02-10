<?php


namespace UnitedStatesLegislators {
    class CurrentDistrictOffices extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentOffices,

            $objectArrayField = "currentOffices";
            //$objectArrayType = "UnitedStatesLegislators\CurrentMember";
        function __construct() {
            $this->route = "legislators-district-offices";
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