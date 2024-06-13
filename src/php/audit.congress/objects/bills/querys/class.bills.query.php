<?php 

namespace AuditCongress {

    class BillsQuery extends AuditCongressQuery {
        
        use GetByIdQuery, GetByBioguideIdQuery, BillsGetWithFilterQuery;

        public function __construct() {
            parent::__construct("Bills");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["updated"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = null) {
            $bills = self::getWithFilterPlusOne($congress, $type, $number, "title", $title);
            if ($sort != null) $bills->setOrderBy($sort, false);
            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>