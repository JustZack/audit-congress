<?php

namespace AuditCongress {

    abstract class MemberTable extends AuditCongressTable {

        private function getTopRow() {
            return $this->getTable()->select(["lastUpdate", "nextUpdate"], null, null, 1)->fetchAssoc();
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid != null) return $this->cacheIsValid;
            
            $row = $this->getTopRow();
            $this->cacheIsValid = $this->nextUpdateIsLater($row);

            return $this->cacheIsValid;
        }

        //By default, use the members Table to enforce cache
        //B/C this keeps ALL member data up to date
        public static function enforceCache() { Members::enforceCache(); }

        public function updateCache() { return false; }
        
        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);
    }
}

?>