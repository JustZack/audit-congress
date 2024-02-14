<?php
namespace UnitedStatesLegislators {
    class PersonIdShort extends \AuditCongress\ApiChildObject {
        public 
            $bioguide,
            $govtrack,
            $thomas;

            function __construct($personShortIdObj) {
                $this->setFieldsFromObject($personShortIdObj);
            }
    }
}
?>