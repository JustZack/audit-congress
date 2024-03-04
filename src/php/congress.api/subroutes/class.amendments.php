<?php


namespace CongressGov {
    class Amendments extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $amendments,

            $apiDataField = "amendments",
            $objectArrayField = "amendments",
            $objectArrayType = "CongressGov\Amendment";

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/amendments";
            $this->setUidFromRoute();

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $amendments = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($amendments, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
        }
    }

    class Amendment extends \AuditCongress\ApiChildObject {
        public
            $congress,
            $type,
            $number,
            
            $updateDate;

            function __construct($amendmentObject) {
                $this->setFieldsFromObject($amendmentObject);
                $this->unsetField("url");
            }
    }
}

?>
