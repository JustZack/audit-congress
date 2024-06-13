<?php 

namespace AuditCongress {

    class BillLawsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetByFilterQuery;

        public function __construct() {
            parent::__construct("BillLaws");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["congress", "number"], false);
        }
    }
}

?>