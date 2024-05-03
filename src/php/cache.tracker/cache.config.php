<?php

namespace Cache {
    class Config {
        private static $settings = null;
        private static $defaultSettings = null;
        
        public static function init($tableName, $settings = null) { 
            CacheTrackerQuery::$tableName = $tableName; 
            if ($settings != null) {
                self::$settings = $settings["caches"];
                self::$defaultSettings = $settings["default"];
            }
        }

        private static function cacheUsesSpecificTimes($settings) {
            return isset($settings["updateTimesIn24HrUTC"]) && count($settings["updateTimesIn24HrUTC"]);
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
    }
}