<?php 

namespace AuditCongress {

    class MemberSOffice extends SqlRow {
        public
            $bioguideId,
            $officeId,

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
    }
}

?>