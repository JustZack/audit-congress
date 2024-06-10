<?php

namespace AuditCongress {

    class AbstractBillItemIndexRow extends AbstractBillItemRow {
        public
            $index;

        public function getColumns() {
            return ["id","billId", "type","congress","number","index"];
        }

        public function getValues() {
            return [$this->id,$this->billId,$this->type,$this->congress,$this->number,$this->index];
        }
    }
}

?>