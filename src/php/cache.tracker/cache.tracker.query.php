<?php

namespace AuditCongress {
    class CacheTrackerQuery extends \MySqlConnector\SqlObject {
        public static $tableName = null;
        public function __construct() {
            parent::__construct(self::$tableName);
        }

        public static function getCacheStatus($cacheName) {
            $cache = new CacheTrackerQuery();
            $cache->setSelectColumns(["*"]);
            $cache->setSearchColumns(["source"]);
            $cache->setSearchValues([$cacheName]);
            $result = $cache->selectFromDB()->fetchAllAssoc();
            if (count($result) > 0) return $result[0];
            else                    return null;
        }

        public static function insertCacheStatus($cacheName, $status, $isRunning) {
            $cache = new CacheTrackerQuery();
            $cache->setColumns(["source", "status", "isRunning"]);
            $cache->setValues([$cacheName, $status, $isRunning]);
            return $cache->insertIntoDB();
        }

        public static function updateCacheStatus($cacheName, $status, $isRunning) {
            $colsToSet = []; $valsToSet = [];
            if ($status != null) {
                array_push($colsToSet, "status"); array_push($valsToSet, $status);
            }
            if (is_bool($isRunning)) {
                array_push($colsToSet, "isRunning"); array_push($valsToSet, $isRunning);
            }

            $cache = new CacheTrackerQuery();
            $cache->setSearchColumns(["source"]);
            $cache->setSearchValues([$cacheName]);
            $cache->setColumns($colsToSet);
            $cache->setValues($valsToSet);
            return $cache->updateInDb();
        }
    }
}

?>