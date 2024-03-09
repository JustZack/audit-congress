<?php


namespace UnitedStatesLegislators {
    class Socials extends \AuditCongress\ApiObject {
        public
            $uid,

            $legislatorSocialMedia,

            $objectArrayField = "legislatorSocialMedia",
            $objectArrayType = "UnitedStatesLegislators\PersonWithSocials";
        function __construct() {
            $this->route = "legislators-social-media";
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