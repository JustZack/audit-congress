<?php


namespace CongressGov {

    class Committees extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $committees,

            $apiDataField = "committees",
            $objectArrayField = "committees",
            $objectArrayType = "CongressGov\Committee";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/committees";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $committees = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($committees, $this->objectArrayField, $this->objectArrayType);
            $this->lowerCaseField("type");
        }
    }

    class Committee extends \AuditCongress\ApiChildObject {
        
        public
            $activities,
            $chamber,
            $name,
            $systemCode,
            $type;
            
            function __construct($committeeObject) {
                $this->setFieldsFromObject($committeeObject);
                $this->lowerCaseField("chamber");
                $this->unsetField("url");
            }
    }
}

?>
