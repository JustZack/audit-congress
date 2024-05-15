<?php 

namespace AuditCongress {

    class BillsQuery extends BillQuery {
        
        use BillsGetByIdQuery, BillsGetByBioguideIdQuery;

        public function __construct() {
            parent::__construct("Bills");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["updated"], false);
        }

        public static function getByFilter($congress = null, $type = null, $number = null, $title = null, $sort = ["updated"]) {
            $bills = new BillsQuery();

            $bills->setBooleanCondition("AND");
            
            if ($congress != null) $bills->addSearchValue("congress", "=", $congress);
            if ($type != null) $bills->addSearchValue("type", "=", $type);
            if ($number != null) $bills->addSearchValue("number", "=", $number);
            if ($title != null) $bills->addSearchValue("title", "like", $title);


            $bills->setOrderBy($sort, false);
            $bills->applyPagination();

            $bills->countInDB();

            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>