<?php

namespace AuditCongress {

    abstract class BillTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null, $rowClassName = null) {
            parent::__construct($tableName, $queryClassName, $rowClassName);
            $this->setTrackedCache("bulk-bill");
        }

        public function updateCache() { 
            $this->cacheTracker->runUpdateScript(true, null, null); 
        }
    }


    //Traits to use in the tables extending BillTable
    trait BillsGetByBillId {
        public static function getByBillId($billId) {
            self::enforceCache();
            $items = self::getQueryClass()::getByBillId($billId);
            return self::parseResult($items);
        }
    }
    trait BillsGetByFilter {
        public static function getByFilter($congress = null, $type = null, $number = null) {
            self::enforceCache();
            $items = self::getQueryClass()::getByFilter($congress, $type, $number);
            return self::parseResult($items);
        }
    }
}

?>