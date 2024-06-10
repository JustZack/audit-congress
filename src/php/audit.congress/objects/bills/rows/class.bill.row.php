<?php

namespace AuditCongress {

    class BillRow extends AbstractBillRow {
        public
            $bioguideId,

            $title,
            $policyArea,

            $introduced,
            $updated;
    
        public function getColumns() { return self::getTableColumns("Bills"); }

        public function getValues() {
            return parent::mergeValues([$this->bioguideId,$this->title,$this->policyArea,$this->introduced,$this->updated]);
        }
    }
}

?>