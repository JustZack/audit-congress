<?php 

namespace AuditCongress {

    class BillSubjectsQuery extends AuditCongressQuery {
        
        use BillsGetByIdQuery, BillsGetByBillIdQuery;

        public function __construct() {
            parent::__construct("BillSubjects");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "subjectIndex"], true);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subjectText = null) {
            $subjects = new BillSubjectsQuery();

            $subjects->setBooleanCondition("AND");

            if ($congress != null) $subjects->addSearchValue("congress", "=", $congress);
            if ($type != null) $subjects->addSearchValue("type", "=", $type);
            if ($number != null) $subjects->addSearchValue("number", "=", $number);
            if ($subjectText != null) $subjects->addSearchValue("subject", "like", $subjectText);

            $subjects->applyDefaultOrder();
            $subjects->applyPagination();
            return $subjects->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>