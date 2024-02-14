<?php

namespace UnitedStatesLegislators {
    class PersonId extends \AuditCongress\ApiChildObject {
        public 
            $bioguide,
            $thomas,
            $lis,
            $govtrack,
            $opensecrets,
            $votesmart,
            $fec,
            $cspan,
            $wikipedia,
            $house_history,
            $ballotpedia,
            $maplight,
            $icpsr,
            $wikidata,
            $google_entity_id; 
        function __construct($idObj) {
            $this->setFieldsFromObject($idObj);
        }
    }
}

?>
