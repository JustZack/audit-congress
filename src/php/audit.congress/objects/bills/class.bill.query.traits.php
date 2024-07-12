<?php

namespace AuditCongress {

    use MySqlConnector\Comparison;

    trait BillsGetByBillIdQuery {

        public static function getByBillId($billid) {
            $query = self::getWithSearchSelect("billId", Comparison::EQUALS, $billid);

            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }
    trait BillsGetByFilterQuery {

        public static function getByFilter($congress = null, $type = null, $number = null) {
            $query = new static();
            
            if ($congress != null) $query->addSearch("congress", Comparison::EQUALS, $congress);
            if ($type != null) $query->addSearch("type", Comparison::EQUALS, $type);
            if ($number != null) $query->addSearch("number", Comparison::EQUALS, $number);

            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }
    trait BillsGetWithFilterQuery {

        public static function getWithFilterPlusOne($congress = null, $type = null, $number = null, $likeSearchColumn = null, $likeSearchValue = null) {
            $query = new static();

            if ($congress != null) $query->addSearch("congress", Comparison::EQUALS, $congress);
            if ($type != null) $query->addSearch("type", Comparison::EQUALS, $type);
            if ($number != null) $query->addSearch("number", Comparison::EQUALS, $number);
            if ($likeSearchColumn != null && $likeSearchValue != null) 
                $query->addSearch($likeSearchColumn, Comparison::LIKE, $likeSearchValue);

            $query->applyPagination();
            return $query;
        }
    }
}

?>