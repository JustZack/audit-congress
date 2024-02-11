<?php


namespace UnitedStatesLegislators {
    class CurrentCommitteeMembership extends \AuditCongress\ApiObject {
        public
            $uid,

            $currentCommitteeMembership,

            $objectArrayField = "currentCommitteeMembership";
            //$objectArrayType = "UnitedStatesLegislators\CurrentMember";
        function __construct() {
            $this->route = "committee-membership-current";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $current = Api::call($this->route);
            $this->setFromApiAsAssocArray($current, $this->objectArrayField);
        }
    }
}

?>