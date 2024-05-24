<?php

namespace AuditCongress {

    use MySqlConnector\SqlRow;
    use MySqlConnector\Table;

    abstract class AuditCongressTable {
        public $queryClassName = null;
        protected $name;
        private ?Table $table = null;

        protected function __construct($tableName, $queryClassName = null) {
            $this->name = $tableName;
            $this->queryClassName = $queryClassName;
         }

        public static function getQueryClass() {
            $inst = static::getInstance();
            return $inst->queryClassName;
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
            $this->getTable()->queueInsert($row);
        }
        public function commitInsert() {
            $this->getTable()->commitInsert();
        }
        public function insertRow(SqlRow $row) {
            $this->getTable()->insert($row);
        }


        public static abstract function getInstance();

        protected static abstract function parseResult($resultRows);

        public static function returnFirst($results) {
            if ($results == null) return null;
            else if (count($results) > 0) return $results[0];
            else return $results;
        }
    }

    trait GetById {
        public static function getById($id) {
            self::enforceCache();
            $items = self::getQueryClass()::getById($id);
            return self::returnFirst(self::parseResult($items));
        }
    }
    trait GetByBioguideId {
        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $items = self::getQueryClass()::getByBioguideId($bioguideId);
            return self::parseResult($items);
        }
    }
}

?>