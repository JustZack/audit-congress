<?php 

namespace AuditCongress {

    class BillTitlesQuery extends BillQuery {

        use BillsGetByIdQuery, BillsGetByBillIdQuery;

        public function __construct() {
            parent::__construct("BillTitles");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "titleIndex"], true);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $titleText = null) {
            $titles = new BillTitlesQuery();

            $titles->setBooleanCondition("AND");

            if ($congress != null) $titles->addSearchValue("congress", "=", $congress);
            if ($type != null) $titles->addSearchValue("type", "=", $type);
            if ($number != null) $titles->addSearchValue("number", "=", $number);
            if ($titleText != null) $titles->addSearchValue("title", "like", $titleText);

            $titles->applyDefaultOrder();
            $titles->applyPagination();
            return $titles->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>