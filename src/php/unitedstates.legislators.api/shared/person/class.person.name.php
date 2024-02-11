<?php

namespace UnitedStatesLegislators {
    class PersonName extends \AuditCongress\ApiChildObject {
        public 
            $first,
            $last,
            $official_full; 
        function __construct($nameObj) {
            $this->setFieldsFromObject($nameObj);
            if (!isset($this->official_full))
                $this->official_full = "$this->first $this->last";
        }
    }
}

?>
