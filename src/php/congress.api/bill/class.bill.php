<?php


namespace CongressGov {

    use BillActions;

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

        function getOption($subRoute) {
            $obj = null;
            $objArgs = [$this->congress, $this->type, $this->number, true];
            switch ($subRoute) {
                case "actions": $obj = new Actions(...$objArgs); break;
                case "amendments": $obj = new Amendments(...$objArgs); break;
                case "committees": $obj = new Committees(...$objArgs); break;
                case "cosponsors": $obj = new Cosponsors(...$objArgs); break;
                case "relatedbills": $obj = new RelatedBills(...$objArgs); break;
                case "subjects": $obj = new Subjects(...$objArgs); break;
                case "summaries": $obj = new Summaries(...$objArgs); break;
                case "text": $obj = new Texts(...$objArgs); break;
                case "titles": $obj = new Titles(...$objArgs); break;
                default: break;
            }

            return $obj;
        }

        static function getOptionList() {
            return ["actions", "amendments", "committees", "cosponsors", "relatedbills", "subjects", "summaries", "text", "titles"];
        }
    }
}

?>
