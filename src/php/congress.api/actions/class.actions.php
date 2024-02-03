<?php


namespace CongressGov {
    class Actions extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $actions;

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->uid = "$this->actionType.$this->congress.$this->type.$this->number.actions";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("$this->actionType/$this->congress/$this->type/$this->number/actions");
            if (isset($result) && isset($result["actions"])) {
                $actions = $result["actions"];
                $this->setFromApiAsArray($actions, "CongressGov\Action", "actions");
                $this->type = strtolower($this->type);
            } else throw new \Exception("CongressGov.Api => $this->actionType/$this->congress/$this->type/$this->number/actions returned null value");
        }
    }

    class Action {
        public
            $actionCode,
            $actionDate,
            $actionTime,
            $committees,
            $sourceSystemCode,
            $sourceSystemName,
            $text,
            $type,

            $recordedVotes;

            function __construct($actionObject) {
                foreach ($actionObject as $key=>$value) $this->{$key} = $value;
            }
    }
}

?>
