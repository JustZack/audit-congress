<?php


namespace CongressGov {
    require_once CONGRESSGOV_FOLDER."\\congress.api.php";
    require_once AUDITCONGRESS_FOLDER."\\abstract.api.object.php";
    class Bill extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $number,
            $type,

            $actions,
            $committees,
            $constitutionalAuthorityStatementText,
            
            $cosponsors,
            $sponsors,

            $introducedDate,
            $updateDate,
            $updateDateIncludingText,

            $latestAction,

            $originChamber,
            $originChamberCode,

            $policyArea,
            $subjects,
            $relatedBills,
            $summaries,
            $textVersions,
            
            $title,
            $titles;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;
        }

        function fetchFromApi() {
            $result = Api::call("bill/$this->congress/$this->type/$this->number");
            if (isset($result) && isset($result["bill"])) {
                $bill = $result["bill"];
                $this->setFromApi($bill);
                $this->type = strtolower($this->type);
                $this->getUid();
            } else throw new \Exception("CongressGov.Api => bill/$this->congress/$this->type/$this->number returned null value");
        }

        function getUid() {
            if (isset($this->uid)) return $this->uid;
            else $this->uid = "bill.$this->congress.$this->type.$this->number";
            return $this->uid;
        }
    }
}

?>
