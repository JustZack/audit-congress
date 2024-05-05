<?php

namespace AuditCongress {
    abstract class CacheTrackedTable extends AuditCongressTable{
        protected ?\Cache\Tracker $cacheTracker = null;
        protected $waitingForCache = false;
        protected function __construct($tableName, $cacheName) {
            parent::__construct($tableName);
            $this->cacheTracker = \Cache\Config::getTracker($cacheName);
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid == null) {
                //If this cache is updating right now, wait for it to update (up to 16 seconds)
                if ($this->cacheTracker->isUpdating()) {
                    //Cache is valid if the updated completed while we waited
                    $this->cacheIsValid = $this->cacheTracker->waitForUpdate();
                    //Set flag that the cache will be valid "soon"
                    if (!$this->cacheIsValid) $this->waitingForCache = true;
                } 
                //Otherwise just check if the cache is out of date
                else {
                    $this->cacheIsValid = !$this->cacheTracker->isOutOfDate();
                    $this->waitingForCache = false;
                }
            }
            return $this->cacheIsValid;
        }

        public function cacheIsUpdating() { return $this->waitingForCache; }

        public static function enforceCache() {
            //Fetch this tables instance
            $tableObj = static::getInstance();
            //If the cache is reported to be invalid (out of date or updating now)
            if (!$tableObj->cacheIsValid()) {
                //If the cache was found to be actively updating, throw an exception to get outa here
                if ($tableObj->cacheIsUpdating())
                    throw new \Cache\WaitingException($tableObj->cacheTracker);
                //Otherwise update the cache
                else $tableObj->updateCache();
            } 
        }

        public abstract function updateCache();

        public static abstract function getInstance();
    }
}

?>