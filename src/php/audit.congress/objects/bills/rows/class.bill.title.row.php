<?php

namespace AuditCongress {

    class BillTitleRow extends AbstractBillItemIndexRow {
        public
            $title,
            $titleAs,
            $titleType,
            $isForPortion;
    
        public function getColumns() { return self::getTableColumns("BillTitles"); }

        public function getValues() {
            return parent::mergeValues([$this->title,$this->titleAs,$this->titleType,$this->isForPortion]);
        }
    }
}

?>