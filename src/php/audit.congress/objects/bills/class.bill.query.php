<?php

namespace AuditCongress {

    abstract class BillQuery extends AuditCongressQuery {

        public abstract function applyDefaultOrder();
    }

    trait BillsGetByIdQuery {

        public static function getById($id) {
            $query = self::getWithSearchSelect("id", "=", $id);
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }
    
    trait BillsGetByBillIdQuery {

        public static function getByBillId($billid) {
            $query = self::getWithSearchSelect("billId", "=", $billid);
            $query->applyDefaultOrder();
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }

    trait BillsGetByBioguideIdQuery {

        public static function getByBioguideId($bioguideId) {
            $bills = self::getWithSearchSelect("bioguideId", "=", $bioguideId);
            $bills->applyDefaultOrder();
            $bills->applyPagination();
            return $bills->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>