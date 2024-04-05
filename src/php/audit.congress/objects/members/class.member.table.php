<?php

namespace AuditCongress {

    abstract class MemberTable extends AuditCongressTable {

        private ?CacheTracker $cacheTracker = null;
        public function __construct($tableName) {
            parent::__construct($tableName);
            $this->cacheTracker = new CacheTracker("bulk-member");
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid == null)
                $this->cacheIsValid = !$this->cacheTracker->isReadyForUpdate();
            return $this->cacheIsValid;
        }

        public function updateCache() {
            $this->cacheTracker->runCachingScript();
            $this->cacheTracker->setUpdated(false, "done");
        }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }
}

?>