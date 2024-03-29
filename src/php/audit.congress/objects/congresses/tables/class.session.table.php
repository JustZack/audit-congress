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

        private static function simpleQuery($function, $argument = null) {
            Congresses::enforceCache();
            $result = ("\AuditCongress\SessionQuery::$function")($argument);
            return self::parseResult($result);
        }

        public static function getByCongress($congressNumber) {
            return self::simpleQuery("getByCongress", $congressNumber);
        }

        public static function getByChamber($chamber) {
            return self::simpleQuery("getByChamber", $chamber);
        }

        public static function getByNumber($number) {
            return self::simpleQuery("getByNumber", $number);
        }

        public static function getByCongressAndNumber($congress, $session) {
            Congresses::enforceCache();
            $result = \AuditCongress\SessionQuery::getByCongressAndNumber($congress, $session);
            return self::parseResult($result);
        }

        public static function getByCongressAndChamber($congress, $chamber) {
            Congresses::enforceCache();
            $result = \AuditCongress\SessionQuery::getByCongressAndChamber($congress, $chamber);
            return self::parseResult($result);
        }

        public static function getByCongressNumberAndChamber($congress, $number, $chamber) {
            Congresses::enforceCache();
            $result = \AuditCongress\SessionQuery::getByCongressNumberAndChamber(
                $congress, $number, $chamber);
            return self::parseResult($result);
        }

        public static function getByNumberAndChamber($number, $chamber) {
            Congresses::enforceCache();
            $result = \AuditCongress\SessionQuery::getByNumberAndChamber($number, $chamber);
            return self::parseResult($result);
        }

        public static function getByDate($date) { 
            return self::simpleQuery("getByDate", $date); 
        }

        public static function getCurrent() {
            Congresses::enforceCache();
            $result = \AuditCongress\SessionQuery::getCurrent();
            return self::parseResult($result);
        }

        public static function getAll() { 
            return self::simpleQuery("getAll"); 
        }
    }
}

?>