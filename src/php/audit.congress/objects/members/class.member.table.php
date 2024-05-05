<?php

namespace AuditCongress {

    abstract class MemberTable extends AuditCongressTable {

        private ?\Cache\Tracker $cacheTracker;
        public function __construct($tableName) {
            parent::__construct($tableName);
            $this->cacheTracker = \Cache\Config::getTracker("bulk-member");
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid == null) {
                //If this cache is updating right now, wait for it to update (up to 16 seconds)
                if ($this->cacheTracker->isUpdating()) {
                    //Cache is valid if the updated completed while we waited
                    $this->cacheIsValid = $this->cacheTracker->waitForUpdate();
                } 
                //Otherwise just check if the cache is out of date
                else $this->cacheIsValid = !$this->cacheTracker->isOutOfDate();
            }
            return $this->cacheIsValid;
        }

        public function updateCache() {
            $this->cacheTracker->runUpdateScript();
            $this->cacheTracker->setRunning(false, "done");
        }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }
}

?>