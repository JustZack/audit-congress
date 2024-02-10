<?php


namespace UnitedStatesLegislators {
//    require_once UNITEDSTATESLEGISLATORS_FOLDER."/api/unitedstates.legislators.api.php";
//    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
    class Socials extends \AuditCongress\ApiObject {
        public
            $uid,

            $legislatorSocialMedia,

            $objectArrayField = "legislatorSocialMedia";
            //$objectArrayType = "UnitedStatesLegislators\HistoricalMember";
        function __construct() {
            $this->route = "legislators-social-media";
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