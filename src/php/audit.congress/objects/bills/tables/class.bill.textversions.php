<?php 

namespace AuditCongress {

    class BillTextVersions extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillTextVersions", "BillTextVersionsQuery", "BillTextVersionRow");
        }
    }
}

?>