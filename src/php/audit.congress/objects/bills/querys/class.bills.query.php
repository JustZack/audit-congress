<?php 

namespace AuditCongress {

    class BillsQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("Bills");
        }

        public static function getById($id) {
            $bills = new BillsQuery();
            $bills->setSearchColumns(["id"]);
            $bills->setSearchValues([$id]);
            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>