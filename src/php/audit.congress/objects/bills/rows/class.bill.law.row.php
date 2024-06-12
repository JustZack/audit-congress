<?php

namespace AuditCongress {

    class BillLawRow extends AbstractBillItemIndexRow {
        public
            $lawType,
            $lawNumber;
    
        public function getColumns() { return self::getTableColumns("BillLaws"); }

        public function getValues() {
            return parent::mergeValues([$this->lawType,$this->lawNumber]);
        }
    }
}

?>