<?php

namespace AuditCongress {

    abstract class MemberTable extends CacheTrackedTable {

        public function __construct($tableName) {
            parent::__construct($tableName, "bulk-member");
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