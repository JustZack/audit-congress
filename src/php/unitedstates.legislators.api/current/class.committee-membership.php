<?php


namespace UnitedStatesLegislators {
//    require_once UNITEDSTATESLEGISLATORS_FOLDER."/api/unitedstates.legislators.api.php";
//    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
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
            $this->setFromApiAsArray($current, $this->objectArrayField);
        }
    }
}

?>