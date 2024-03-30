<?php 

namespace AuditCongress {

    class Sessions extends AuditCongressTable {
        
        private function __construct() {
            parent::__construct("Sessions");
        }

        private static $sessionTable = null;
        public static function getInstance() {
            if (self::$sessionTable == null) 
                self::$sessionTable = new Sessions();
            return self::$sessionTable;
        }

        public function cacheIsValid() { 
            return Congresses::getInstance()->cacheIsValid();
        }

        public function updateCache() { return False; }

        public function insertSessions($congress, $sessions) {
            foreach ($sessions as $session) {                
                $session["congress"] = $congress;
                $session = new SessionRow($session);
                $this->queueInsert($session);
            }
        }

        protected static function parseResult($resultRows) {
            return SessionRow::rowsToObjects($resultRows);
        }

        private static function genericQuery($function, ...$arguments) {
            Congresses::enforceCache();
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


        private static function getByMultiple($congress, $number, $chamber) {
            $data = false;
            if (isset($congress) && isset($number) && isset($chamber))
                $data = self::getByCongressNumberAndChamber($congress, $number, $chamber);
            else if (isset($congress) && isset($number))
                $data = self::getByCongressAndNumber($congress, $number);
            else if (isset($congress) && isset($chamber))
                $data = self::getByCongressAndChamber($congress, $chamber);
            else if (isset($number) && isset($chamber))
                $data = self::getByNumberAndChamber($number, $chamber);
            return $data;
        }

        private static function getByIndividual($congress, $number, $chamber) {
            $data = false;
            if (isset($congress)) $data = self::getByCongress($congress);
            if (isset($number))   $data = self::getByNumber($number);
            if (isset($chamber))  $data = self::getByChamber($chamber);
            return $data;
        }
        public static function getByCongressNumberOrChamber($congress, $number, $chamber) {
            $result = self::getByMultiple($congress, $number, $chamber);
            if ($result == false) $result = self::getByIndividual($congress, $number, $chamber);
            return $result;
            
        }
    }
}

?>