<?php

namespace AuditCongress {

    class BillRelatedBillRow extends AbstractBillItemIndexRow {
        public
            $reason,
            $identifier,
            
            $relatedBillId,
            
            $relatedType,
            $relatedNumber,
            $relatedCongress;

    
        public function getColumns() { return self::getTableColumns("BillRelatedBills"); }

        public function getValues() {
            return parent::mergeValues([$this->reason,$this->identifier,$this->relatedBillId,
                                        $this->relatedType,$this->relatedNumber,$this->relatedCongress]);
        }
    }
}

?>