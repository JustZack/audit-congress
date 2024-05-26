<?php

namespace AuditCongress {
    abstract class CacheTrackedTable extends AuditCongressTable{
        protected ?\Cache\Tracker $cacheTracker = null;
        protected $cacheIsValid = null;
        
        protected function __construct($tableName, $queryClassName = null, $rowClassName = null, $cacheName, $cacheTimeout = 5) {
            parent::__construct($tableName, $queryClassName, $rowClassName);
            $this->cacheTracker = \Cache\Config::getTracker($cacheName);
            $this->cacheTracker->setTimeout($cacheTimeout);
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid == null) {
                //If this cache is updating right now, wait for it to update (up to 16 seconds)
                if ($this->cacheTracker->isUpdating()) {
                    //Try waiting for the cache to finish updating
                    //Throws exception if waited too long
                    //I dont love this, but it does break out from trying to fetch data
                    $this->cacheIsValid = $this->cacheTracker->waitForUpdate();
                } 
                //Otherwise just check if the cache is out of date
                else $this->cacheIsValid = !$this->cacheTracker->isOutOfDate();
            }
            return $this->cacheIsValid;
        }

        /*
            @throws \Cache\WaitingException
        */
        public static function enforceCache() {
            //Fetch this tables instance
            $tableObj = static::getInstance();
            //If the cache is reported to be invalid (out of date) update it
            if (!$tableObj->cacheIsValid()) $tableObj->updateCache();
        }

        public abstract function updateCache();
    }
}

?>