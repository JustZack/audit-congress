<?php 

namespace AuditCongress {

    class BillCommitteeReportsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetByFilterQuery;

        public function __construct() {
            parent::__construct("BillCommitteeReports");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["type", "congress", "number", "index"], true);
        }
    }
}

?>