<?php

namespace AuditCongress {

    class BillCommitteeReportRow extends AbstractBillItemIndexRow {
        public
            $reportType,
            $reportNumber,
            $reportCongress;

    
        public function getColumns() { return self::getTableColumns("BillCommitteeReports"); }

        public function getValues() {
            return parent::mergeValues([$this->reportType,$this->reportNumber,$this->reportCongress]);
        }
    }
}

?>