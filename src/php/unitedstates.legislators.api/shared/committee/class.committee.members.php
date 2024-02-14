<?php

namespace UnitedStatesLegislators {
    class CommitteeMembers implements \JsonSerializable {
        public 
            $committeeMembers;
        
        function __construct($committeeMembers) {
            $this->committeeMembers = array();
            foreach ($committeeMembers as $key=>$member)
                array_push($this->committeeMembers, new CommitteeMember($member));
        }

        function jsonSerialize() {
            return $this->committeeMembers;
        }
    }

    class CommitteeMember extends \AuditCongress\ApiChildObject {

        public
            $name,
            $party,
            $rank,
            $title,
            $bioguide;

        function __construct($committeeMember) {
            $this->setFieldsFromObject($committeeMember);
        }
    }
}

?>