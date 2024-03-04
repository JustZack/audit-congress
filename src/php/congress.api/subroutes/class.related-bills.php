<?php


namespace CongressGov {
    class RelatedBills extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $relatedBills,

            $apiDataField = "relatedBills",
            $objectArrayField = "relatedBills",
            $objectArrayType = "CongressGov\RelatedBill";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/relatedbills";
            $this->setUidFromRoute();

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $relatedBills = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($relatedBills, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
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
