<?php

namespace AuditCongress {

    abstract class MemberTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null) {
            parent::__construct($tableName, $queryClassName, "bulk-member");
        }

        public function updateCache() { $this->cacheTracker->runUpdateScript(); }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }
}

?>