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
            $query = self::getWithSearchSelect("bioguideId", "=", $bioguideId);
            $query->applyDefaultOrder();
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }

    trait BillsGetByFilterQuery {

        public static function getWithFilterPlusOne($congress = null, $type = null, $number = null, $likeSearchColumn = null, $likeSearchValue = null) {
            $query = new static();

            $query->setBooleanCondition("AND");

            if ($congress != null) $query->addSearchValue("congress", "=", $congress);
            if ($type != null) $query->addSearchValue("type", "=", $type);
            if ($number != null) $query->addSearchValue("number", "=", $number);
            if ($likeSearchColumn != null && $likeSearchValue != null) 
                $query->addSearchValue($likeSearchColumn, "like", $likeSearchValue);

            $query->applyDefaultOrder();
            $query->applyPagination();
            return $query;
        }
    }
}

?>