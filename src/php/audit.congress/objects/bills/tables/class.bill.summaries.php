<?php 

namespace AuditCongress {

    class BillSummaries extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillSummaries", "BillSummariesQuery", "BillSummaryRow");
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $text = null) {
            self::enforceCache();
            $titles = BillSummariesQuery::getByFilter($congress, $type, $number, $text);
            return self::parseResult($titles);
        }
    }
}

?>