<?php 

namespace AuditCongress {

    class BillTextVersionsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery;

        public function __construct() {
            parent::__construct("BillTextVersions");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["date"], false);
        }
    }
}

?>