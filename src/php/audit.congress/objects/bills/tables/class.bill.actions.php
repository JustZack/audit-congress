<?php 

namespace AuditCongress {

    class BillActions extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillActions", "BillActionsQuery", "BillActionRow");
        }
    }
}

?>