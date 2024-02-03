<?php


namespace CongressGov {

    class Committees extends \AuditCongress\ApiObject {
        public
            $uid,

            $congress,
            $type,
            $number,

            $committees;

        function __construct($congress, $type, $number) {
            $this->congress = $congress;
            $this->type = strtolower($type);
            $this->number = $number;

            $this->route = "bill/$this->congress/$this->type/$this->number/committees";
            $this->setUidFromRoute();
        }

        function fetchFromApi() {
            $result = Api::call_bulk($this->route);
            if (isset($result) && isset($result["committees"])) {
                $committees = $result["committees"];
                $this->setFromApiAsArray($committees, "committees", "CongressGov\Committee");
                $this->lowerCaseField("type");
            } else throw new \Exception("CongressGov.Api => $this->route returned null value");
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
