<?php

namespace AuditCongress {

    class AbstractBillRow extends AuditCongressRow {
        public
            $id,

            $type,
            $congress,
            $number;
    
        public function getColumns() {
            return ["id","type","congress","number"];
        }

        public function getValues() {
            return [$this->id,$this->type,$this->congress,$this->number];
        }
    }
}

?>