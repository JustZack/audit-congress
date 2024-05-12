<?php 

namespace AuditCongress {

    class BillTitlesQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("BillTitles");
        }

        public function applyIdOrder() {
            $this->setOrderBy(["billId", "titleIndex"], true);
        }

        public static function getById($id) {
            $title = self::getWithSearchSelect("id", "=", $id);
            return $title->selectFromDB()->fetchAllAssoc();
        }

        public static function getByBillId($billid) {
            $title = self::getWithSearchSelect("billId", "=", $billid);
            $title->applyIdOrder();
            $title->applyPagination();
            return $title->selectFromDB()->fetchAllAssoc();
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $titleText = null) {
            $titles = new BillTitlesQuery();

            $titles->setBooleanCondition("AND");

            if ($congress != null) $titles->addSearchValue("congress", "=", $congress);
            if ($type != null) $titles->addSearchValue("type", "=", $type);
            if ($number != null) $titles->addSearchValue("number", "=", $number);
            if ($titleText != null) $titles->addSearchValue("title", "like", $titleText);

            $titles->applyIdOrder();
            $titles->applyPagination();
            return $titles->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>