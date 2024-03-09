<?php

namespace AuditCongress {

    use MySqlConnector\SqlRow;
    use MySqlConnector\Table;

    abstract class AuditCongressTable {
        protected $name;
        private ?Table $table = null;
        protected $cacheIsValid = null;

        protected function __construct($tableName) {
            $this->name = $tableName;
         }

        protected function getTable() { 
            if ($this->table == null) $this->table = new Table($this->name);
            return $this->table; 
        }

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
                $tableObj->updateCache();
            }
        }

        protected static function setUpdateTimes($rowArray) {
            $rowArray["lastUpdate"] = time();
            $rowArray["nextUpdate"] = time()+(60*60*24*7);
            return $rowArray;
        }

        protected function nextUpdateIsLater($row) {
            if ($row != null) {
                $next = (int)$row["nextUpdate"];
                return !($next == false || $next < time());
            } else return false;
        }

        public static abstract function getInstance();

        public abstract function cacheIsValid();

        public abstract function updateCache();

        public static function returnFirst($results) {
            if ($results == null) return null;
            else if (count($results) > 0) return $results[0];
            else return $results;
        }

    }
}

?>