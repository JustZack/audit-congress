<?php 

namespace AuditCongress {

    class BillCommitteesQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery;

        public function __construct() {
            parent::__construct("BillCommittees");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "date"], false);
        }
    }
}

?>