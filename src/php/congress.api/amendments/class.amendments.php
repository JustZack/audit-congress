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

            $this->uid = "$this->actionType.$this->congress.$this->type.$this->number.amendments";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("$this->actionType/$this->congress/$this->type/$this->number/amendments");
            if (isset($result) && isset($result["amendments"])) {
                $amendments = $result["amendments"];
                $this->setFromApiAsArray($amendments, "CongressGov\Amendment", "amendments");
                $this->type = strtolower($this->type);
            } else throw new \Exception("CongressGov.Api => $this->actionType/$this->congress/$this->type/$this->number/amendments returned null value");
        }
    }

    class Amendment {
        public
            $congress,
            $type,
            $number,
            
            $updateDate;

            function __construct($amendmentObject) {
                unset($amendmentObject["url"]);
                foreach ($amendmentObject as $key=>$value) $this->{$key} = $value;
            }
    }
}

?>
