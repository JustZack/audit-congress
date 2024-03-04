<?php
namespace ProPublica {
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

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $committee = Api::call($this->route, "results")[0];
            $this->setFromApi($committee);
        }
    }
}

?>