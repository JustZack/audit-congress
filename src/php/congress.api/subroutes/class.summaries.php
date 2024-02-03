<?php


namespace CongressGov {
    class Summaries extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $summaries;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->uid = "bill.$this->congress.$this->type.$this->number.summaries";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("bill/$this->congress/$this->type/$this->number/summaries");
            if (isset($result) && isset($result["summaries"])) {
                $summaries = $result["summaries"];
                $this->setFromApiAsArray($summaries, "summaries", "CongressGov\Summary");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => bill/$this->congress/$this->type/$this->number/summaries returned null value");
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