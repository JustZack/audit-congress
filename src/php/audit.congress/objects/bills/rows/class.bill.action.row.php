<?php

namespace AuditCongress {

    class BillActionRow extends AbstractBillItemIndexRow {
        public
            $actionType,
            $text,
            $acted;
    
        public function getColumns() { return self::getTableColumns("BillActions"); }

        public function getValues() {
            return parent::mergeValues([$this->actionType,$this->text,$this->acted]);
        }
    }
}

?>