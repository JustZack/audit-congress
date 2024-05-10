<?php

namespace AuditCongress {

    class BillCosponsorRow extends \MySqlConnector\SqlRow {
        public
            $id,

            $type,
            $congress,
            $number,

            $bioguideId,

            $sponsoredAt,
            $withdrawnAt,

            $isOriginal;
    
        public function getColumns() {
            return ["id","type","congress","number","bioguideId","sponsoredAt","withdrawnAt","isOriginal"];
        }

        public function getValues() {
            return [$this->id,$this->type,$this->congress,$this->number,
            $this->bioguideId,$this->sponsoredAt,$this->withdrawnAt,$this->isOriginal];
        }
    }
}

?>