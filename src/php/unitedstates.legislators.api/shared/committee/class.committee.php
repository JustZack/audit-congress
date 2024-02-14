<?php

namespace UnitedStatesLegislators {
    class Committee extends \AuditCongress\ApiChildObject {
        public 
            $type,
            $name,
            $url,
            $minority_url,
            $thomas_id,
            $house_committee_id,
            $address,
            $phone,
            $rss_url,
            $youtube_id,
            $jurisdiction,
            $jurisdiction_source;
            
        public SubCommittees $subcommittees;

        function __construct($committeeObj) {
            if (isset($committeeObj["subcommittees"])) {
                $subcommittees = $committeeObj["subcommittees"];
                unset($committeeObj["subcommittees"]);
                $this->subcommittees = new SubCommittees($subcommittees);
            }

            $this->setFieldsFromObject($committeeObj);
        }
    }
}

?>