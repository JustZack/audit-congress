<?php

namespace AuditCongress {

    class MemberOfficesRow extends \MySqlConnector\SqlRow {
        public
            $bioguideId,$officeId,
            $address,$suite,$building,$city,$state,$zip,
            $latitude,$longitude,$phone,$fax;

        public function getColumns() {
            return ["bioguideId","officeId","address","suite",
                    "building","city","state","zip","latitude",
                    "longitude","phone","fax"];
        }

        public function getValues() {
            return [$this->bioguideId,$this->officeId,$this->address,
            $this->suite,$this->building,$this->city,$this->state,
            $this->zip,$this->latitude,$this->longitude,$this->phone,$this->fax];
        }
    }
}

?>