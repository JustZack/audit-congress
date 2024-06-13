<?php 

namespace AuditCongress {

    class BillRelatedBillsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetByFilterQuery;

        public function __construct() {
            parent::__construct("BillRelatedBills");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["congress", "type", "number", "index"], true);
        }
    }
}

?>