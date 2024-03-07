<?php

namespace AuditCongress {

    use \MySqlConnector\SqlRow;

    class MemberOfficesRow extends SqlRow {
        public
            $bioguideId,$officeId,
            $address,$suite,$building,$city,$state,$zip,
            $latitude,$longitude,$phone,$fax,
            $lastUpdate,$nextUpdate;

        public function getColumns() {
            return ["bioguideId","officeId","address","suite",
                    "building","city","state","zip","latitude",
                    "longitude","phone","fax","lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguideId,$this->officeId,$this->address,
            $this->suite,$this->building,$this->city,$this->state,
            $this->zip,$this->latitude,$this->longitude,$this->phone,
            $this->fax,$this->lastUpdate,$this->nextUpdate];
        }
    }
}

?>