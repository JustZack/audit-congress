<?php

namespace Cache {
    class TrackerQuery extends \MySqlConnector\SqlObject {
        public static $tableName = null;
        public function __construct() {
            parent::__construct(self::$tableName);
        }

        public static function getCacheStatus($cacheName) {
            $cache = new TrackerQuery();
            $cache->setSelectColumns(["*"]);
            $cache->setSearchColumns(["source"]);
            $cache->setSearchValues([$cacheName]);
            $result = $cache->selectFromDB()->fetchAllAssoc();
            if (count($result) > 0) return $result[0];
            else                    return null;
        }

        private static function getSetColsAndVals($status, $isRunning, $lastRunStart, $lastUpdate, $nextUpdate) {
            $colsToSet = []; $valsToSet = [];
            if ($status != null) {
                array_push($colsToSet, "status"); array_push($valsToSet, $status);
            }
            if (is_bool($isRunning)) {
                array_push($colsToSet, "isRunning"); array_push($valsToSet, $isRunning);
            }
            if ($lastRunStart != null) {
                array_push($colsToSet, "lastRunStart"); array_push($valsToSet, $lastRunStart);
            }
            if ($lastUpdate != null) {
                array_push($colsToSet, "lastUpdate"); array_push($valsToSet, $lastUpdate);
            }
            if ($nextUpdate != null) {
                array_push($colsToSet, "nextUpdate"); array_push($valsToSet, $nextUpdate);
            }
            return ["columns" => $colsToSet, "values" => $valsToSet];
        }

        public static function insertCacheStatus($cacheName, $status = null, $isRunning = null, $lastRunStart = null, $lastUpdate = null, $nextUpdate = null) {
            $setItems = self::getSetColsAndVals($status, $isRunning, $lastRunStart, $lastUpdate, $nextUpdate);
            list("columns" => $colsToSet, "values" => $valsToSet) = $setItems;

            array_push($colsToSet, "source"); array_push($valsToSet, $cacheName);

            $cache = new TrackerQuery();
            $cache->setColumns($colsToSet);
            $cache->setValues($valsToSet);
            return $cache->insertIntoDB();
        }

        public static function updateCacheStatus($cacheName, $status = null, $isRunning = null, $lastRunStart = null, $lastUpdate = null, $nextUpdate = null) {
            $setItems = self::getSetColsAndVals($status, $isRunning, $lastRunStart, $lastUpdate, $nextUpdate);
            list("columns" => $colsToSet, "values" => $valsToSet) = $setItems;
            
            $cache = new TrackerQuery();
            $cache->setSearchColumns(["source"]);
            $cache->setSearchValues([$cacheName]);
            $cache->setColumns($colsToSet);
            $cache->setValues($valsToSet);
            return $cache->updateInDb();
        }
    }
}

?>