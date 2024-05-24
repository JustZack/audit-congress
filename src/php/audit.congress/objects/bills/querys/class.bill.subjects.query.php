<?php 

namespace AuditCongress {

    class BillSubjectsQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetWithFilterQuery;

        public function __construct() {
            parent::__construct("BillSubjects");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "subjectIndex"], true);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subject = null) {
            $subjects = self::getWithFilterPlusOne($congress, $type, $number, "subject", $subject);
            return $subjects->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>