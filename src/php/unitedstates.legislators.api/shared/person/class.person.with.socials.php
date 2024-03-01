<?php

namespace UnitedStatesLegislators {
    class PersonWithSocials extends \AuditCongress\ApiChildObject {
        public PersonIdShort $id;
        public PersonSocials $socials;
        
        function __construct($socialPersonObj) {
            $this->id = new PersonIdShort($socialPersonObj["id"]);
            $this->socials = new PersonSocials($socialPersonObj["social"]);
        }

        public function getSocials() { return $this->socials; }
    }
}

?>
