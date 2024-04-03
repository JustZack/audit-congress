<?php

namespace Util {

    use DateTime;

    class Time {
        public static function hoursToSeconds($hours) { return $hours*60*60; }
        public static function secondsToHours($seconds) { return (int)($seconds/60/60)%24; }

        public static function getDateTimeStr($secondsSinceEpoch) {
            return date("Y-m-d H:i:s", $secondsSinceEpoch);
        }

        public static function getNowDateTimeStr() {
            return self::getDateTimeStr(time());
        }

        public static function getFirstHourPastNow($hoursArray) {
            $currentHour = self::secondsToHours(time());
            foreach ($hoursArray as $hour) if ($currentHour < $hour) return $hour;
            return -1;
        }

        public static function getDateTime($day, $month, $year, $hours, $minutes, $seconds) {

        }

        public static function getTimeForTomorrow($hours, $minutes, $seconds) {

        }

        public static function getTimeForToday($hours, $minutes, $seconds) {
            
        }
    }
}

?>