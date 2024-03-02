<?php

namespace AuditCongress {

    use MySqlConnector\SqlRow;
    use MySqlConnector\Table;

    abstract class MemberTables {
        private $name;
        private ?Table $table = null;
        protected $cacheIsValid = null;

        protected function __construct($tableName) {
            $this->name = $tableName;
         }

        protected function getTable() { 
            if ($this->table == null) $this->table = new Table($this->name);
            return $this->table; 
        }

        private function getTopRow() {
            $table = $this->getTable();
            return $table->select(["lastUpdate", "nextUpdate"], null, null, 1)->fetchAssoc();
        }

        private function rowIsValid($row) {
            if ($row != null) {
                $next = (int)$row["nextUpdate"];
                return !($next == false || $next < time());
            } else return false;
        }

        public function cacheIsValid() {
            if ($this->cacheIsValid != null) return $this->cacheIsValid;

            $row = $this->getTopRow();
            $this->cacheIsValid = $this->rowIsValid($row);

            return $this->cacheIsValid;
        }

        protected abstract function updateCache();

        protected function clearRows() {
            //Clear out all data associated with this table
            $this->getTable()->truncate();
        }

        public function queueInsert(SqlRow $row) {
            $this->getTable()->queueInsert($row->getColumns(), $row->getValues());
        }
        public function commitInsert() {
            $this->getTable()->commitInsert();
        }
        public function insertRow(SqlRow $row) {
            $this->getTable()->insert($row->getColumns(), $row->getValues());
        }

        public static function enforceCache() {
            $tableObj = static::getInstance();
            if (!$tableObj->cacheIsValid()) {
                var_dump("Update cache for: ".$tableObj->name);
                $tableObj->updateCache();
            }
        }

        protected static function setUpdateTimes($rowArray) {
            $rowArray["lastUpdate"] = time();
            $rowArray["nextUpdate"] = time()+(60*60*24*7);
            return $rowArray;
        }

        public static abstract function getInstance();

    }
}

?>