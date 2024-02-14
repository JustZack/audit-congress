<?php

namespace UnitedStatesLegislators {
    class PersonBio extends \AuditCongress\ApiChildObject {
        public
            $birthday,
            $gender;
        function __construct($bioObj) {
            $this->setFieldsFromObject($bioObj);
        }
    }
}

?>
