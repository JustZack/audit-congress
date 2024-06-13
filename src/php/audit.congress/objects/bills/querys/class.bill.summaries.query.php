<?php 

namespace AuditCongress {

    class BillSummariesQuery extends AuditCongressQuery {

        use GetByIdQuery, BillsGetByBillIdQuery, BillsGetWithFilterQuery;

        public function __construct() {
            parent::__construct("BillSummaries");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["updated"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $text = null) {
            $titles = self::getWithFilterPlusOne($congress, $type, $number, "text", $text);
            return $titles->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>