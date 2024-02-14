<?php

namespace UnitedStatesLegislators {
    class PersonOffice extends \AuditCongress\ApiChildObject {
        public
            $id,
            $address,
            $suite,
            $building,
            $city,
            $state,
            $zip,
            $latitude,
            $longitude,
            $phone,
            $fax;

        function __construct($officeObj) {
            $this->setFieldsFromObject($officeObj);
        }
    }

    class PersonOffices implements \JsonSerializable {
        public 
            $offices;
        function __construct($officesObj) {
            $this->offices = array();
            foreach ($officesObj as $key=>$office)
                array_push($this->offices, new PersonOffice($office));
        }

        function jsonSerialize() {
            return $this->offices;
        }
    }
}

?>
