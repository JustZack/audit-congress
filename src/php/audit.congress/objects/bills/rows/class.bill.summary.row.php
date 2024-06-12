<?php

namespace AuditCongress {

    class BillSummaryRow extends AbstractBillItemIndexRow {
        public
            $text,
            $description,
            $date,
            $updated;
    
        public function getColumns() { return self::getTableColumns("BillSummaries"); }

        public function getValues() {
            return parent::mergeValues([$this->text,$this->description,$this->date,$this->updated]);
        }
    }
}

?>