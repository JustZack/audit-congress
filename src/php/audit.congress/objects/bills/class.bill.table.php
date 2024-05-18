<?php

namespace AuditCongress {

    abstract class BillTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null) {
            parent::__construct($tableName, $queryClassName, "bulk-bill");
        }

        public function updateCache() { 
            $this->cacheTracker->runUpdateScript(true, null, null); 
        }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }


    //Traits to use in the tables extending BillTable
    trait BillsGetById {
        public static function getById($id) {
            self::enforceCache();
            $items = self::getQueryClass()::getById($id);
            return self::returnFirst(self::parseResult($items));
        }
    }
    trait BillsGetByBioguideId {
        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $items = self::getQueryClass()::getByBioguideId($bioguideId);
            return self::parseResult($items);
        }
    }
    trait BillsGetByBillId {
        public static function getByBillId($billId) {
            self::enforceCache();
            $items = self::getQueryClass()::getByBillId($billId);
            return self::parseResult($items);
        }
    }
}

?>