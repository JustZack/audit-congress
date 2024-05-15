<?php 

namespace AuditCongress {

    class BillsQuery extends BillQuery {
        
        use BillsGetByIdQuery, BillsGetByBioguideIdQuery, BillsGetByFilterQuery;

        public function __construct() {
            parent::__construct("Bills");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["updated"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = ["updated"]) {
            $bills = self::getWithFilterPlusOne($congress, $type, $number, "title", $title);
            $bills->setOrderBy($sort, false);
            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>