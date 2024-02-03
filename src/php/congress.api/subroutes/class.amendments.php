<?php


namespace CongressGov {
    class Amendments extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $amendments;

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/amendments";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call_bulk($this->route);
            if (isset($result) && isset($result["amendments"])) {
                $amendments = $result["amendments"];
                $this->setFromApiAsArray($amendments, "amendments", "CongressGov\Amendment");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
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
