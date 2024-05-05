<?php

namespace Cache {
    class Config {
        private static $settings = null;
        private static $defaultSettings = null;
        private static $isset = false;
        
        public static function init($tableName, $settings = null) { 
            //The tracker needs to know what table to look at for tracking
            if ($tableName == null) throw \Exception("\Config\Config::init tableName cannot be null.");
            else TrackerQuery::$tableName = $tableName; 

            if ($settings != null) {
                self::$settings = $settings["caches"];
                self::$defaultSettings = $settings["default"];
            }
            self::$isset = True;
        }

        public static function isset() { return self::$isset && isset(TrackerQuery::$tableName); }

        private static function settingsUse24HrTimes($settings) {
            return isset($settings["updateTimesIn24HrUTC"]) && count($settings["updateTimesIn24HrUTC"]);
        }

        private static function setNeededDefaults($settings) {
            //Ensure required fields are set via the default settings if they are not present.
            if (!self::settingsUse24HrTimes($settings) && !isset($settings["updateIntervalInHours"]))
                $settings["updateIntervalInHours"] = self::$defaultSettings["updateIntervalInHours"];
            if (!isset($settings["status"]))
                $settings["status"] = self::$defaultSettings["status"];
            return $settings;
        }

        public static function getTracker($cacheName) {
            if (!self::isset()) throw new \Exception("CacheTracker: Must call Cache\Config::init before building a tracker.");
            $trackerSettings = null;
            
            //If this cache is defined in the settings array
            if (isset(self::$settings[$cacheName]))
                //Pull it and set any missing fields via the default settings
                $trackerSettings = self::setNeededDefaults(self::$settings[$cacheName]);
            else
                //Otherwise use the default settings
                $trackerSettings = self::$defaultSettings;

            return new Tracker($cacheName, $trackerSettings);
        }
    }
}