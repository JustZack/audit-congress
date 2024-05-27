<?php 

namespace AuditCongress {

    class Bills extends BillTable {

        use \Util\GetInstance, GetById, GetByBioguideId;

        private function __construct() {
            parent::__construct("Bills", "BillsQuery", "BillRow");
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = ["updated"]) {
            self::enforceCache();
            $bills = BillsQuery::getByFilter($congress, $type, $number, $title, $sort);
            return self::parseResult($bills);
        }
    }
}

?>