<?php

namespace AuditCongress {

    class BillCommitteeRow extends AbstractBillItemIndexRow {
        public 
            $thomasId,
            $action,
            $date;
    
        public function getColumns() { return self::getTableColumns("BillCommittees"); }

        public function getValues() {
            return parent::mergeValues([$this->thomasId,$this->action,$this->date]);
        }
    }
}

?>