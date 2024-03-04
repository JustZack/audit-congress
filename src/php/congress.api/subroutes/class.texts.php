<?php


namespace CongressGov {
    class Texts extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $actionType,

            $texts,

            $apiDataField = "textVersions",
            $objectArrayField = "texts",
            $objectArrayType = "CongressGov\Text";

        function __construct($congress, $type, $number, $isBill) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->actionType = $isBill ? "bill" : "amendment";

            $this->route = "$this->actionType/$this->congress/$this->type/$this->number/text";
            $this->setUidFromRoute();

            $this->fetchFromApi();
        }

        function fetchFromApi() {
            $texts = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($texts, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
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
