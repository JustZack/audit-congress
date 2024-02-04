<?php
namespace ProPublica {
    require_once PROPUBLICA_FOLDER."/api/propublica.api.php";
    require_once AUDITCONGRESS_FOLDER."/abstract.api.object.php";
    class Committee extends \AuditCongress\ApiObject {
        public 
            $uid,

            $congress,
            $chamber,
            $committeeId;

        function __construct($congressNumber, $chamber, $committeeId) {
            $this->congress = $congressNumber;
            $this->chamber = $chamber;
            $this->committeeId = $committeeId;

            $this->route = "$this->congress/$this->chamber/committees/$this->committeeId";
            $this->setUidFromRoute();
            $this->route .= ".json";
        }

        function fetchFromApi() {
            $committee = Api::call($this->route, "results")[0];
            $this->setFromApi($committee);
        }
    }
}

?>