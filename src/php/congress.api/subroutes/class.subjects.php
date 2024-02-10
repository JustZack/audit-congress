<?php


namespace CongressGov {
    class Subjects extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $legislativeSubjects,
            $policyArea,

            $apiDataField = "subjects",
            $objectArrayField = "legislativeSubjects",
            $objectArrayType = "CongressGov\Subject";

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/subjects";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $subjects = Api::call_bulk($this->route, $this->apiDataField);
            $this->setFromApiAsArray($subjects["legislativeSubjects"], $this->objectArrayField, $this->objectArrayType);
            if (isset($subjects["policyArea"])) $this->policyArea = new Subject($subjects["policyArea"]);
            $this->lowerCaseField("type");
        }
    }

    class Subject extends \AuditCongress\ApiChildObject {
        public
            $name;
            
            function __construct($subjectObject) {
                $this->setFieldsFromObject($subjectObject);
            }
    }
}

?>