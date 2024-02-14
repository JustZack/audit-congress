<?php

namespace UnitedStatesLegislators {
    class SubCommittee extends \AuditCongress\ApiChildObject {
        public 
            $name,
            $thomas_id,
            $address,
            $phone;
        
            function __construct($subcommitteeObj) {
                $this->setFieldsFromObject($subcommitteeObj);
            }

    }

    class SubCommittees implements \JsonSerializable {
        public 
            $subcommittees;
        
            function __construct($subcommitteesObj) {
                $this->subcommittees = array();
                foreach ($subcommitteesObj as $key=>$subcommittee)
                    array_push($this->subcommittees, new SubCommittee($subcommittee));
            }
    
            function jsonSerialize() {
                return $this->subcommittees;
            }
    }
}

?>