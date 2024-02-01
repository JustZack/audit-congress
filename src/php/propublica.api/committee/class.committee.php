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
        }

        function fetchFromApi() {
            $result = Api::call("$this->congress/$this->chamber/committees/$this->committeeId.json");
            if (isset($result) && isset($result["results"]) && isset($result["results"][0])) {
                $bill = $result["results"][0];
                $this->setFromApi($bill);
                $this->getUid();
            } else throw new \Exception("ProPublica.Api => $this->congress/$this->chamber/committees/$this->committeeId.json returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else {
                $this->uid = "committee.$this->congress.$this->chamber.$this->committeeId";
            }
            return $this->uid;
        }
    }
}

?>