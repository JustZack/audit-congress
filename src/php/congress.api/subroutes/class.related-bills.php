<?php


namespace CongressGov {
    class RelatedBills extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $relatedBills;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->uid = "bill.$this->congress.$this->type.$this->number.relatedbills";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("bill/$this->congress/$this->type/$this->number/relatedbills");
            if (isset($result) && isset($result["relatedBills"])) {
                $relatedBills = $result["relatedBills"];
                $this->setFromApiAsArray($relatedBills, "relatedBills", "CongressGov\RelatedBill");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => bill/$this->congress/$this->type/$this->number/relatedbills returned null value");
        }
    }

    class RelatedBill extends \AuditCongress\ApiChildObject {
        public
            $congress,
            $number,
            $type,

            $latestAction,
            $relationshipDetails,
            
            $title;

            function __construct($relatedBillsObject) {
                $this->setFieldsFromObject($relatedBillsObject);
                $this->lowerCaseField("type");
                $this->unsetField("url");
            }
    }
}

?>
