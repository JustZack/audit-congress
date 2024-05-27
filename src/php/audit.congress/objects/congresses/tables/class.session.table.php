<?php 

namespace AuditCongress {

    class Sessions extends CongressTable {
        
        use \Util\GetInstance;

        private function __construct() {
            parent::__construct("Sessions", "SessionQuery", "SessionRow");
        }

        private static function genericQuery($function, ...$arguments) {
            self::enforceCache();
            $result = ("\AuditCongress\SessionQuery::$function")(...$arguments);
            return self::parseResult($result);
        }

        public static function getByCongress($congressNumber) {
            return self::genericQuery("getByCongress", $congressNumber);
        }

        public static function getByChamber($chamber) {
            return self::genericQuery("getByChamber", $chamber);
        }

        public static function getByNumber($number) {
            return self::genericQuery("getByNumber", $number);
        }

        public static function getByCongressAndNumber($congress, $session) {
            return self::genericQuery("getByCongressAndNumber", $congress, $session);
        }

        public static function getByCongressAndChamber($congress, $chamber) {
            return self::genericQuery("getByCongressAndChamber", $congress, $chamber);
        }

        public static function getByCongressNumberAndChamber($congress, $number, $chamber) {
            return self::genericQuery("getByCongressNumberAndChamber", $congress, $number, $chamber);
        }

        public static function getByNumberAndChamber($number, $chamber) {
            return self::genericQuery("getByNumberAndChamber", $number, $chamber);
        }

        public static function getByDate($date) { 
            return self::genericQuery("getByDate", $date); 
        }

        public static function getCurrent() {
            return self::genericQuery("getCurrent");
        }

        public static function getAll() { 
            return self::genericQuery("getAll"); 
        }


        private static function getByTwo($congress, $number, $chamber) {
            if (isset($congress) && isset($number))
                return self::getByCongressAndNumber($congress, $number);
            if (isset($congress) && isset($chamber))
                return self::getByCongressAndChamber($congress, $chamber);
            if (isset($number) && isset($chamber))
                return self::getByNumberAndChamber($number, $chamber);
            return false;
        }

        private static function getByOne($congress, $number, $chamber) {
            if (isset($congress)) return self::getByCongress($congress);
            if (isset($number))   return self::getByNumber($number);
            if (isset($chamber))  return self::getByChamber($chamber);
            return false;
        }

        public static function getByCongressNumberOrChamber($congress, $number, $chamber) {
            if (isset($congress) && isset($number) && isset($chamber))
                return self::getByCongressNumberAndChamber($congress, $number, $chamber);
            $result = self::getByTwo($congress, $number, $chamber);
            if ($result == false) $result = self::getByOne($congress, $number, $chamber);
            return $result;
            
        }
    }
}

?>