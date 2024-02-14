<?php

namespace UnitedStatesLegislators {
    class PersonWithOffices extends \AuditCongress\ApiChildObject {
        public PersonIdShort $id;
        public PersonOffices $offices;
        
        function __construct($officePersonObj) {
            $this->id = new PersonIdShort($officePersonObj["id"]);
            $this->offices = new PersonOffices($officePersonObj["offices"]);
        }
    }
}

?>
