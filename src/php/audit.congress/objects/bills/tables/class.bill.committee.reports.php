<?php 

namespace AuditCongress {

    class BillCommitteeReports extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId, BillsGetByFilter;

        private function __construct() {
            parent::__construct("BillCommitteeReports", "BillCommitteeReportsQuery", "BillCommitteeReportRow");
        }
    }
}

?>