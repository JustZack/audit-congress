<?php


namespace UnitedStatesLegislators {
    class CurrentDistrictOffices extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentOffices,

            $objectArrayField = "currentOffices",
            $objectArrayType = "UnitedStatesLegislators\PersonWithOffices";
        function __construct() {
            $this->route = "legislators-district-offices";
            $this->setUidFromRoute();
            $this->route .= ".json";

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $current = Api::call($this->route);
            $this->setFromApiAsAssocArray($current, $this->objectArrayField, $this->objectArrayType);
        }
    }
}

?>