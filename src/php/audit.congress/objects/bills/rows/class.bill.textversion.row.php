<?php

namespace AuditCongress {

    class BillTextVersionRow extends AbstractBillItemIndexRow {
        public
            $versionType,
            $url,
            $format,
            $date;
    
        public function getColumns() { return self::getTableColumns("BillTextVersions"); }

        public function getValues() {
            return parent::mergeValues([$this->versionType,$this->url,$this->format,$this->date]);
        }
    }
}

?>