<?php

namespace AuditCongress {

    use MySqlConnector\InsertGroup;
    use MySqlConnector\SqlRow;
    use MySqlConnector\Table;

    abstract class AuditCongressTable {
        public $queryClassName = null;
        public $rowClassName = null;
        protected $name;
        private ?Table $table = null;

        protected function __construct($tableName, $queryClassName = null, $rowClassName = null) {
            $this->name = $tableName;
            $this->queryClassName = "\AuditCongress\\$queryClassName";
            $this->rowClassName = "\AuditCongress\\$rowClassName";
         }

        public static function getQueryClass() {
            $inst = static::getInstance();
            return $inst->queryClassName;
        }

        public static function getRowClass() {
            $inst = static::getInstance();
            return $inst->rowClassName;
        }

        protected function getTable() { 
            if ($this->table == null) $this->table = new Table($this->name);
            return $this->table; 
        }

        //All Tables are singletons. Use \Util\GetInstance on each class.
        public static abstract function getInstance();

        public static function parseResult($resultRows) {
            return self::getRowClass()::rowsToObjects($resultRows);
        }


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
    trait TruncateRows {
        public static function truncateRows() {
            self::getQueryClass()::truncateRows();
        }
    }
    trait InsertQueueing {
        public static function queueInsert(SqlRow $row) {
            self::getQueryClass()::queueRowInsert($row);
        }
        public static function commitInsert() {
            return self::getQueryClass()::commitRowInsert();
        }
    }
}

?>