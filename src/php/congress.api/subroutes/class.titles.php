<?php


namespace CongressGov {
    class Titles extends \AuditCongress\ApiObject {
        public
            $congress,
            $type,
            $number,

            $titles,

            $apiDataField = "titles",
            $objectArrayField = "titles",
            $objectArrayType = "CongressGov\Title";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/titles";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $titles = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($titles, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
        }
    }

    class Title extends \AuditCongress\ApiChildObject {
        public
            $title,
            $titleType,
            $chamberCode,
            $chamberName,
            $billTextVersionName,
            $billTextVersionCode;
            
            function __construct($subjectObject) {
                $this->setFieldsFromObject($subjectObject);
                $this->lowerCaseField("chamberName");
                $this->lowerCaseField("chamberCode");
            }
    }
}

?>