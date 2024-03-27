<?php

namespace AuditCongress {

    class CacheTracker {
        public $cacheName;
        public function __construct($cacheName) {
            $this->cacheName = $cacheName;
            if (CacheTrackerQuery::$tableName == null)
                throw new \Exception("CacheTracker: Must call static::initCacheTracker first");
        }
        
        public static function initCacheTracker($tableName) { CacheTrackerQuery::$tableName = $tableName; }

        public function getRow() {
            return CacheTrackerQuery::getCacheStatus($this->cacheName);
        }

        private function getCacheColumn($column) {
            $row = $this->getRow();
            if ($row != null) return $row[$column];
            else              return null;
        }

        public function getSource() { return $this->getCacheColumn("source"); }

        public function getStatus() { return $this->getCacheColumn("status"); }

        public function isRunning() { return $this->getCacheColumn("isRunning"); }

        public function isSet() { return $this->getRow() != null; }
 
        public function setCacheStatus($status, $isRunning) {
            $function = "\AuditCongress\CacheTrackerQuery::updateCacheStatus";
            if (!$this->isSet()) $function = "\AuditCongress\CacheTrackerQuery::insertCacheStatus";
            $function($this->cacheName, $status, $isRunning);
        }

        public function setRunning($isRunning) { $this->setCacheStatus(null, $isRunning); }
        public function setStatus($status) { $this->setCacheStatus($status, null); }

    }
}

?>