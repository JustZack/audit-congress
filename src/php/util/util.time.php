<?php

namespace Util {

    use DateTimeZone;

    class Time {
        public static function hoursToSeconds($hours) { return $hours*60*60; }
        public static function secondsToHours($seconds) { return (int)($seconds/60/60)%24; }

        public static function getDateTimeInZone($secondsSinceEpoch, $timeZone = "UTC") {
            switch ($timeZone) {
                case "UTC": break;
                default: throw new \OutOfBoundsException("Passed Unknown Timezone to getDateTimeStr: $timeZone");
            }
            $Date = new \DateTime('NOW', new \DateTimeZone($timeZone));
            return $Date->setTimestamp($secondsSinceEpoch);
        }

        public static function getHour($secondsSinceEpoch, $timeZone = "UTC") {
            $Date = self::getDateTimeInZone($secondsSinceEpoch, $timeZone);
            return intval($Date->format("H"));
        }

        public static function getDateTimeStr($secondsSinceEpoch, $timeZone = "UTC") {
            $Date = self::getDateTimeInZone($secondsSinceEpoch, $timeZone);
            return $Date->format("Y-m-d H:i:s");
        }

        public static function getNowDateTimeStr($timeZone = "UTC") {
            return self::getDateTimeStr(time(), $timeZone);
        }

        public static function getFirstHourPastNow($hoursArray, $timeZone = "UTC") {
            $currentHour = self::getHour(time(), $timeZone);
            foreach ($hoursArray as $hour) if ($currentHour < $hour) return $hour;
            return -1;
        }

    }
}

?>