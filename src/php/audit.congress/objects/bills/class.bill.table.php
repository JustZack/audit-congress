<?php

namespace AuditCongress {

    abstract class BillTable extends CacheTrackedTable {

        public function __construct($tableName) {
            parent::__construct($tableName, "bulk-bill");
        }

        public function updateCache() { 
            $this->cacheTracker->runUpdateScript(false, null, null); 
        }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }
}

?>