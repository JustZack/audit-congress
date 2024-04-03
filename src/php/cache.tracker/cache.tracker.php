<?php

namespace AuditCongress {

    class CacheTracker {
        public 
            $cacheName,
            $cacheSettings,
            $cacheRow = null;
        private static $settings = null;
        private static $defaultSettings = null;

        public function __construct($cacheName) {
            $this->cacheName = $cacheName;
            if (CacheTrackerQuery::$tableName == null)
                throw new \Exception("CacheTracker: Must call static::initCacheTracker first");
            if (isset(self::$settings) || isset(self::$defaultSettings)) 
                $this->cacheSettings = self::getCacheSettings($cacheName);
        }
        


        public static function initCacheTracker($tableName, $settings = null) { 
            CacheTrackerQuery::$tableName = $tableName; 
            if ($settings != null) {
                self::$settings = $settings["caches"];
                self::$defaultSettings = $settings["default"];
            }
        }

        private static function cacheUsesSpecificTimes($settings) {
            return isset($settings["updateTimesIn24HrUTC"]) && count($settings["updateTimesIn24HrUTC"]);
        }

        private static function cacheUsesScript($settings) {
            return isset($settings["scriptPath"]);
        }

        private static function setNeededDefaults($settings) {
            //Ensure required fields are set via the default settings if they are not present.
            if (!self::cacheUsesSpecificTimes($settings) && !isset($settings["updateIntervalInHours"]))
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
            if ($this->cacheRow == null) 
                $this->cacheRow = CacheTrackerQuery::getCacheStatus($this->cacheName);
            return $this->cacheRow;
        }

        private function getCacheColumn($column) {
            $row = $this->getRow();
            if ($row != null) return $row[$column];
            else              return false;
        }

        public function getNextCacheUpdate() {
            $nextUpdate = 0;
            //If updateTimesIn24Hr has values, use these as the basis for $nextUpdate
            if (self::cacheUsesSpecificTimes($this->cacheSettings)) {
                $updateHours = $this->cacheSettings["updateTimesIn24HrUTC"];
                
                $nextHour = \Util\Time::getFirstHourPastNow($updateHours);
                $offset = 0;
                //$nextHour == -1 => time() is later than all given hours
                //  So instead use the first hour for the next day
                if ($nextHour == -1) {
                    $nextHour = $updateHours[0];
                    $offset = \Util\Time::hoursToSeconds(24);
                }
                $d = new \DateTime(date("Y-m-d $nextHour:00:00"));
                $nextUpdate = $d->getTimestamp() + $offset;
            } else {
                $nextUpdate = time() + \Util\Time::hoursToSeconds($this->cacheSettings["updateIntervalInHours"]);
            }

            return \Util\Time::getDateTimeStr($nextUpdate);
        }



        public function getSource() { return $this->getCacheColumn("source"); }

        public function getStatus() { return $this->getCacheColumn("status"); }

        public function isRunning() { return $this->getCacheColumn("isRunning"); }

        public function isSet() { return $this->getRow() != null; }

        public function isOutOfDate() { return strtotime($this->getCacheColumn("nextUpdate")) < time(); }

        public function isReadyForUpdate() { return !$this->isRunning() && $this->isOutOfDate(); }



        public function runCachingScript($waitForComplete = true) {
            $out = array();
            if (self::cacheUsesScript($this->cacheSettings)) {
                $runner = $this->cacheSettings["scriptRunner"];
                $path = \Util\File::getAbsolutePath($this->cacheSettings["scriptPath"]);
                $dir = \Util\File::getFolderPath($path);
                $file = \Util\File::getFileName($path);

                //$post = !$waitForComplete ? " > /dev/null &" : "";
                $cmd = "cd $dir && $runner $file";
                array_push($out, $cmd);
                exec($cmd, $out);
            }
            return $out;
        }

        public function setCacheStatus($status = null, $isRunning = null, $lastUpdate = null, $nextUpdate = null) {
            $function = "\AuditCongress\CacheTrackerQuery::updateCacheStatus";
            if (!$this->isSet()) $function = "\AuditCongress\CacheTrackerQuery::insertCacheStatus";
            $function($this->cacheName, $status, $isRunning, $lastUpdate, $nextUpdate);
            $this->cacheRow = null;
        }

        public function setStatus($status) { $this->setCacheStatus($status); }

        public function setRunning($isRunning, $status = null) { 
            if ($isRunning) 
                $this->setCacheStatus($status, $isRunning, \Util\Time::getNowDateTimeStr(), null); 
            else 
                $this->setCacheStatus($status, $isRunning, null, $this->getNextCacheUpdate()); 
        }
    }
}

?>