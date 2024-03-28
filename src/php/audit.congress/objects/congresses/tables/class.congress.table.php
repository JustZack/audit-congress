<?php 

namespace AuditCongress {

    class Congresses extends AuditCongressTable {
        
        private function __construct() {
            parent::__construct("Congresses");
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

            if (self::$cacheTracker == null) 
                self::$cacheTracker = new CacheTracker("bulk-congress");
            if (!self::$cacheTracker->isSet()) {
                self::$cacheTracker->setCacheStatus("", 0);
                $this->cacheIsValid = false;
            } else {
                $status = self::$cacheTracker->getStatus();
                $this->cacheIsValid = $status == "done";
            }

            return $this->cacheIsValid;
        }

        public function updateCache() {
            self::$cacheTracker->setRunning(true);
            try {
                $congresses = new \CongressGov\Congresses();
                $this->insertCongresses($congresses->congresses);
                $this->commitInsert();
                $this->cacheIsValid = true;
            } catch(\Exception $e) { }
            self::$cacheTracker->setCacheStatus("done", false);
        }

        public function insertCongresses($congresses) {
            foreach ($congresses as $congress) {                
                $congress = new CongressRow($congress);
                $this->queueInsert($congress);
            }
        }

        protected static function parseResult($resultRows) {
            return CongressRow::rowsToObjects($resultRows);
        }

        public static function getByNumber($congressNumber) {
            self::enforceCache();
            $congress = CongressQuery::getByNumber($congressNumber);
            return self::parseResult($congress);
        }

        public static function getAll() {
            self::enforceCache();
            $congresses = CongressQuery::getAll();
            return self::parseResult($congresses);
        }
    }
}

?>