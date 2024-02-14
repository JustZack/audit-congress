<?php

namespace UnitedStatesLegislators {
    class PersonTerm extends \AuditCongress\ApiChildObject {
        public
            $type,
            $start,
            $end,
            $state,
            $district,
            $party,
            $how;
        function __construct($termObj) {
            $this->setFieldsFromObject($termObj);
            if ($this->type == "prez") {
                unset($this->district);
                unset($this->state);
            } else if ($this->type == "sen") {
                unset($this->district);
                unset($this->how);
            } else {
                unset($this->how);
            }
        }
    }

    class PersonTerms implements \JsonSerializable {
        public 
            $terms;
        function __construct($termsObj) {
            $this->terms = array();
            foreach ($termsObj as $key=>$term)
                array_push($this->terms, new PersonTerm($term));
        }

        function jsonSerialize() {
            return $this->terms;
        }
    }
}

?>
