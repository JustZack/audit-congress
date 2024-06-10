<?php 

namespace AuditCongress {

    class BillActionsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery;

        public function __construct() {
            parent::__construct("BillActions");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "acted"], false);
        }
    }
}

?>