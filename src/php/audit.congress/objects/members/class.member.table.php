<?php

namespace AuditCongress {

    use MySqlConnector\SqlRow;
    use MySqlConnector\Table;

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

        public static function enforceCache() {
            $tableObj = static::getInstance();
            if (!$tableObj->cacheIsValid()) {
                $tableObj->updateCache();
            }
        }

        public static abstract function getInstance();

        public function updateCache() { return false; }

    }
}

?>