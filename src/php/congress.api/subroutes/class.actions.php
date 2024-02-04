<?php


namespace CongressGov {
    class Actions extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $actions,

            $apiDataField = "actions",
            $objectArrayField = "actions",
            $objectArrayType = "CongressGov\Action";

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/actions";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $actions = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($actions, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
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
