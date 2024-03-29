<?php 

namespace AuditCongress {

    class Congresses extends AuditCongressTable {
        
        private function __construct() {
            parent::__construct("Congresses");
            self::$cacheTracker = new CacheTracker("bulk-congress");
        }

        private static $congressTable = null;
        public static function getInstance() {
            if (self::$congressTable == null) 
                self::$congressTable = new Congresses();
            return self::$congressTable;
        }

        private static $cacheTracker = null;
        public function cacheIsValid() {
            if ($this->cacheIsValid != null) return $this->cacheIsValid;

            $status = self::$cacheTracker->getStatus();

            $this->cacheIsValid = $status == "done";

            return $this->cacheIsValid;
        }

        public function updateCache() {
            self::$cacheTracker->setRunning(true);
            $sessionsInstance = Sessions::getInstance();
            
            $congresses = new \CongressGov\Congresses();

            $this->clearRows();
            $sessionsInstance->clearRows();

            $this->insertCongresses($congresses->congresses);

            $this->commitInsert();
            $sessionsInstance->commitInsert();
            
            $this->cacheIsValid = true;

            self::$cacheTracker->setCacheStatus("done", false);
        }

        public function insertCongresses($congresses) {
            $sessionsInstance = Sessions::getInstance();
            foreach ($congresses as $congress) {  
                $sessions = $congress->sessions;
                $congress = new CongressRow($congress);
                $current = $congress->number;
                $sessionsInstance->insertSessions($current, $sessions);
                $this->queueInsert($congress);
            }
        }

        protected static function parseResult($resultRows) {
            return CongressRow::rowsToObjects($resultRows);
        }

        private static function simpleQuery($function, $argument = null) {
            self::enforceCache();
            $result = ("\AuditCongress\CongressQuery::$function")($argument);
            return self::parseResult($result);
        }

        public static function getByNumber($congressNumber) {
            $result = self::simpleQuery("getByNumber", $congressNumber);
            return self::returnFirst($result);
        }

        public static function getByYear($year) { 
            $result = self::simpleQuery("getByYear", $year); 
            return self::returnFirst($result);
        }

        public static function getCurrent() {
            return self::getByYear(date("Y"));
        }

        public static function getAll() { 
            return self::simpleQuery("getAll"); 
        }
    }
}

?>