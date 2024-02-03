<?php


namespace CongressGov {
    class Titles extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $titles;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->uid = "bill.$this->congress.$this->type.$this->number.titles";
        }

        function fetchFromApi() {
            $result = Api::call_bulk("bill/$this->congress/$this->type/$this->number/titles");
            if (isset($result) && isset($result["titles"])) {
                $titles = $result["titles"];
                $this->setFromApiAsArray($titles, "titles", "CongressGov\Title");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => bill/$this->congress/$this->type/$this->number/titles returned null value");
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
                $this->lowerCaseField("chamber");
                $this->lowerCaseField("chamberCode");
            }
    }
}

?>