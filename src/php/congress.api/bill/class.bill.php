<?php


namespace CongressGov {
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
            $titles,

            $apiDataField = "bill";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $bill = Api::call($this->route, $this->apiDataField);
            $this->setFromApi($bill);
            $this->lowerCaseField("type");
        }
    }
}

?>
