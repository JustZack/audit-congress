<?php


namespace CongressGov {
    class Summaries extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $summaries,

            $apiDataField = "summaries",
            $objectArrayField = "summaries",
            $objectArrayType = "CongressGov\Summary";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/summaries";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $summaries = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($summaries, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
        }
    }

    class Summary extends \AuditCongress\ApiChildObject {
        public
            $actionDate,
            $actionDesc,
            $text,
            $updateDate,
            $versionCode;
            
            function __construct($subjectObject) {
                $this->setFieldsFromObject($subjectObject);
            }
    }
}

?>