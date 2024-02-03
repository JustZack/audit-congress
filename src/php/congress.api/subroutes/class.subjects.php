<?php


namespace CongressGov {
    class Subjects extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $legislativeSubjects,
            $policyArea;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->uid = "bill.$this->congress.$this->type.$this->number.subjects";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("bill/$this->congress/$this->type/$this->number/subjects");
            if (isset($result) && isset($result["subjects"])) {
                $subjects = $result["subjects"];
                $this->setFromApiAsArray($subjects["legislativeSubjects"], "legislativeSubjects", "CongressGov\Subject");
                $this->policyArea = new Subject($subjects["policyArea"]);
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => bill/$this->congress/$this->type/$this->number/subjects returned null value");
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