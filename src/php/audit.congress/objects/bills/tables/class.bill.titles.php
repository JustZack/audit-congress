<?php 

namespace AuditCongress {

    class BillTitles extends BillTable {

        use \Util\GetInstance, GetById, BillsGetByBillId;

        private function __construct() {
            parent::__construct("BillTitles", "BillTitlesQuery", "BillTitleRow");
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null) {
            self::enforceCache();
            $titles = BillTitlesQuery::getByFilter($congress, $type, $number, $title);
            return self::parseResult($titles);
        }
    }
}

?>