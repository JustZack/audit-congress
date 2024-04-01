<?php

namespace AuditCongress {

    use DateTime;

    class CacheTracker {
        public $cacheName;
        private static $settings = null;
        private static $defaultSettings = null;

        public function __construct($cacheName) {
            $this->cacheName = $cacheName;
            if (CacheTrackerQuery::$tableName == null)
                throw new \Exception("CacheTracker: Must call static::initCacheTracker first");
        }
        
        public static function initCacheTracker($tableName, $settings) { 
            CacheTrackerQuery::$tableName = $tableName; 
            self::$settings = $settings["caches"];
            self::$defaultSettings = $settings["default"];
        }

        private static function getTimeStr($secondsSinceEpoch) {
            return date("Y-m-d H:i:s", $secondsSinceEpoch);
        }

        private static function settingsUseSpecificTimes($settings) {
            return isset($settings["updateTimesIn24HrUTC"]) && count($settings["updateTimesIn24HrUTC"]);
        }

        private static function getNextCacheUpdate($settings) {
            $nextUpdate = 0;
            //If updateTimesIn24Hr has values, use these as the basis for $nextUpdate
            if (self::settingsUseSpecificTimes($settings)) {
                $currentHour = (int)(time()/60/60)%24;
                $updateHours = $settings["updateTimesIn24HrUTC"];
                foreach ($updateHours as $hour) {
                    if ($currentHour < $hour) {
                        $d = new DateTime(date("Y-m-d $hour:00:00"));
                        $nextUpdate = $d->getTimestamp();
                        break;
                    }
                }
            } else $nextUpdate = time() + $settings["updateIntervalInHours"]*60*60;

            return self::getTimeStr($nextUpdate);
        }

        public static function getCacheSettings($cacheName) {
            $cSettings = null;
            
            if (isset(self::$settings[$cacheName])) {
                $cSettings = self::$settings[$cacheName];
                //Ensure some required fields are set via the default settings if not present.
                if (!self::settingsUseSpecificTimes($cSettings) && !isset($cSettings["updateIntervalInHours"]))
                    $cacheSettings["updateIntervalInHours"] = self::$defaultSettings["updateIntervalInHours"];
                if (!isset($cSettings["status"]))
                    $cacheSettings["status"] = self::$defaultSettings["status"];
            } else $cSettings = self::$defaultSettings;

            return $cSettings;
        }

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