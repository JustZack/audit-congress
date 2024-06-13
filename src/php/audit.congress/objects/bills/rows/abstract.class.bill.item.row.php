<?php

namespace AuditCongress {

    class AbstractBillItemRow extends AbstractBillRow {
        public $billId;

        public function getColumns() {
            return ["id","billId", "type","congress","number"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number];
        }
    }
}

?>