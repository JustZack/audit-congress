<?php

namespace AuditCongress {

    abstract class MemberTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null, $rowClassName = null) {
            parent::__construct($tableName, $queryClassName, $rowClassName, "bulk-member");
        }

        public function updateCache() { $this->cacheTracker->runUpdateScript(); }
    }
}

?>