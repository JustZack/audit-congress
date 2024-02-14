<?php

namespace UnitedStatesLegislators {
    class Person extends \AuditCongress\ApiChildObject {
        public PersonId $id;
        public PersonName $name;
        public PersonBio $bio;
        public PersonTerms $terms;
        
        function __construct($personObj) {
            $this->id = new PersonId($personObj["id"]);
            $this->name = new PersonName($personObj["name"]);
            $this->bio = new PersonBio($personObj["bio"]);
            $this->terms = new PersonTerms($personObj["terms"]);
        }
    }
}

?>
