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

        public static function getByCongressTypeNumber($congress, $type, $number) {
            $bills = new BillsQuery();
            $bills->setSearchColumns(["congress", "type", "number"]);
            $bills->setSearchValues([$congress, $type, $number]);
            return $bills->selectFromDB()->fetchAllAssoc();
        }

        public static function getByCongressAndType($congress, $type) {
            $bills = new BillsQuery();
            $bills->setSearchColumns(["congress", "type"]);
            $bills->setSearchValues([$congress, $type]);
            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>