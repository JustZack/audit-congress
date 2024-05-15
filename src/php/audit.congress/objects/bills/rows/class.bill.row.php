<?php

namespace AuditCongress {

    class BillRow extends \MySqlConnector\SqlRow {
        public
            $id,

            $type,
            $congress,
            $number,

            $bioguideId,

            $title,

            $introduced,
            $updated;
    
        public function getColumns() {
            return ["id","type","congress","number","bioguideId","title","introduced","updated"];
        }

        public function getValues() {
            return [$this->id,$this->type,$this->congress,$this->number,
            $this->bioguideId,$this->title,$this->introduced,$this->updated];
        }
    }
}

?>