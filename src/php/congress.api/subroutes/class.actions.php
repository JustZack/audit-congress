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

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/actions";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call_bulk($this->route);
            if (isset($result) && isset($result["actions"])) {
                $actions = $result["actions"];
                $this->setFromApiAsArray($actions, "actions", "CongressGov\Action");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
        }
    }

    class Action extends \AuditCongress\ApiChildObject {
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
                $this->setFieldsFromObject($actionObject);
            }
    }
}

?>
