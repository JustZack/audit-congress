<?php 

namespace AuditCongress {

    class BillLaws extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId, BillsGetByFilter;

        private function __construct() {
            parent::__construct("BillLaws", "BillLawsQuery", "BillLawRow");
        }
    }
}

?>