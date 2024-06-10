<?php

namespace AuditCongress {

    class BillCosponsorRow extends AbstractBillItemRow {
        public
            $bioguideId,

            $sponsoredAt,
            $withdrawnAt,

            $isOriginal;
    
        public function getColumns() { return self::getTableColumns("BillCoSponsors"); }

        public function getValues() {
            return parent::mergeValues([$this->bioguideId,$this->sponsoredAt,$this->withdrawnAt,$this->isOriginal]);
        }
    }
}

?>