<?php
namespace ProPublica {
    require_once "propublica.api.php";
    require_once "../audit.congress/abstract.api.object.php";
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
            $bill = $result["results"][0];
            $this->setFromApi($bill);
            $this->getUid();
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