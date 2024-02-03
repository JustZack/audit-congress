<?php


namespace CongressGov {
    class Texts extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $texts;

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/text";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call_bulk($this->route);
            if (isset($result) && isset($result["textVersions"])) {
                $texts = $result["textVersions"];
                $this->setFromApiAsArray($texts, "texts", "CongressGov\Text");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
        }
    }

    class Text extends \AuditCongress\ApiChildObject {
        public
            $type,
            $date,
            $formats;
            
            function __construct($textObject) {
                $this->setFieldsFromObject($textObject);
            }
    }
}

?>
