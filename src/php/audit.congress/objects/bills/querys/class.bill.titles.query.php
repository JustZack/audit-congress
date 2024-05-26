<?php 

namespace AuditCongress {

    class BillTitlesQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetWithFilterQuery;

        public function __construct() {
            parent::__construct("BillTitles");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["billId", "titleIndex"], true);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null) {
            $titles = self::getWithFilterPlusOne($congress, $type, $number, "title", $title);
            return $titles->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>