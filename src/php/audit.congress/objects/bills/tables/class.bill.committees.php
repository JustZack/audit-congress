<?php 

namespace AuditCongress {

    class BillCommittees extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillCommittees", "BillCommitteesQuery", "BillCommitteeRow");
        }
    }
}

?>