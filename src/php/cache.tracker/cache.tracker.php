<?php

namespace AuditCongress {

    use DateTime;

    class CacheTracker {
        public 
            $cacheName,
            $cacheSettings;
        private static $settings = null;
        private static $defaultSettings = null;

        public function __construct($cacheName) {
            $this->cacheName = $cacheName;
            if (CacheTrackerQuery::$tableName == null)
                throw new \Exception("CacheTracker: Must call static::initCacheTracker first");
            if (self::hasDefinedSettings()) $this->cacheSettings = self::getCacheSettings($cacheName);
        }
        
        private static function hasDefinedSettings() {
            return isset(self::$settings) || isset(self::$defaultSettings);
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

        private static function hoursToSeconds($hours) { return $hours*60*60; }
        private static function secondsToHours($seconds) { return (int)($seconds/60/60)%24; }

        private static function getFirstHourPastNow($hoursArray) {
            $currentHour = self::secondsToHours(time());
            foreach ($hoursArray as $hour) if ($currentHour < $hour) return $hour;
            return -1;
        }

        public function getNextCacheUpdate() {
            $nextUpdate = 0;
            //If updateTimesIn24Hr has values, use these as the basis for $nextUpdate
            if (self::settingsUseSpecificTimes($this->cacheSettings)) {
                $updateHours = $this->cacheSettings["updateTimesIn24HrUTC"];
                $nextHour = self::getFirstHourPastNow($updateHours);
                $offset = 0;
                //$nextHour == -1 => time() is later than all given hours
                //  So instead use the first hour for the next day
                if ($nextHour == -1) {
                    $nextHour = $updateHours[0];
                    $offset = self::hoursToSeconds(24);
                }
                $d = new DateTime(date("Y-m-d $nextHour:00:00"));
                $nextUpdate = $d->getTimestamp() + $offset;
            } else {
                $nextUpdate = time() + self::hoursToSeconds($this->cacheSettings["updateIntervalInHours"]);
            }

            return self::getTimeStr($nextUpdate);
        }

        private static function setNeededDefaults($settings) {
            //Ensure required fields are set via the default settings if they are not present.
            if (!self::settingsUseSpecificTimes($settings) && !isset($settings["updateIntervalInHours"]))
                $settings["updateIntervalInHours"] = self::$defaultSettings["updateIntervalInHours"];
            if (!isset($settings["status"]))
                $settings["status"] = self::$defaultSettings["status"];
            return $settings;
        }

        public static function getCacheSettings($cacheName) {
            $cSettings = null;
            
            //If this cache is defined in the settings array
            if (isset(self::$settings[$cacheName]))
                //Pull it and set any missing fields via the default settings
                $cSettings = self::setNeededDefaults(self::$settings[$cacheName]);
            else
                //Otherwise use the default settings
                $cSettings = self::$defaultSettings;

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