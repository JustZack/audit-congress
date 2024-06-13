<?php

namespace AuditCongress {

    class MemberOfficesRow extends AuditCongressRow {
        public
            $bioguideId,$id,
            $address,$suite,$building,$city,$state,$zip,
            $latitude,$longitude,$phone,$fax;

        public function getColumns() { return self::getTableColumns("MemberOffices"); }

        public function getValues() {
            return [$this->bioguideId,$this->id,$this->address,
            $this->suite,$this->building,$this->city,$this->state,
            $this->zip,$this->latitude,$this->longitude,$this->phone,$this->fax];
        }
    }
}

?>