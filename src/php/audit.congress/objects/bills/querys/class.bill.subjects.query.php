<?php 

namespace AuditCongress {

    class BillSubjectsQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("BillSubjects");
        }

        public function applyIdOrder() {
            $this->setOrderBy(["billId", "subjectIndex"], true);
        }

        public static function getById($id) {
            $subject = self::getWithSearchSelect("id", "=", $id);
            return $subject->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBillId($billid) {
            $subjects = self::getWithSearchSelect("billId", "=", $billid);
            $subjects->applyIdOrder();
            $subjects->applyPagination();
            return $subjects->selectFromDB()->fetchAllAssoc();
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $subjectText = null) {
            $subjects = new BillSubjectsQuery();

            $subjects->setBooleanCondition("AND");

            if ($congress != null) $subjects->addSearchValue("congress", "=", $congress);
            if ($type != null) $subjects->addSearchValue("type", "=", $type);
            if ($number != null) $subjects->addSearchValue("number", "=", $number);
            if ($subjectText != null) $subjects->addSearchValue("subject", "like", $subjectText);

            $subjects->applyIdOrder();
            $subjects->applyPagination();
            return $subjects->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>