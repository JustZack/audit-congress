<?php 

namespace AuditCongress {

    class BillRelatedBills extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId, BillsGetByFilter;

        private function __construct() {
            parent::__construct("BillRelatedBills", "BillRelatedBillsQuery", "BillRelatedBillRow");
        }
    }
}

?>