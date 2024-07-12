<?php

namespace AuditCongress {

    use MySqlConnector\Comparison;
    use MySqlConnector\InsertGroup;
    use MySqlConnector\SqlRow;

    abstract class AuditCongressQuery extends \MySqlConnector\QueryWrapper {
        protected static function runAdvancedQuery($sql) : \MySqlConnector\Result {
            return \MySqlConnector\Query::getResult($sql);
        }

        protected static function getWithSearchSelect($column, $equalityOperator, $value) {
            //Anything that implements this is meant to override the constructor.
            $theQuery = new static();
            $theQuery->addSearch($column, $equalityOperator, $value);
            return $theQuery;
        }

        protected abstract function applyDefaultOrder();

        protected function applyPagination() {
            $pagination = \API\Runner::getPagination();
            $this->setLimit($pagination->pageSize());
            $this->setOffset($pagination->offset());
            $this->applyDefaultOrder();
        }
    }

    trait GetByBioguideIdQuery {
        public static function getByBioguideId($bioguideId) {
            $query = self::getWithSearchSelect("bioguideId", Comparison::EQUALS, $bioguideId);
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }

    trait GetByIdQuery {
        public static function getById($id) {
            $query = self::getWithSearchSelect("id", Comparison::EQUALS, $id);
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }
    
    trait TruncateRowsQuery {
        public static function truncateRows() {
            $query = new static(); 
            $query->truncate();
        }
    }

    trait InsertQueueingQuery {
        private static $insertQueue = null;
        public static function queueRowInsert(SqlRow $row) {
            if (self::$insertQueue == null) self::$insertQueue = new static(); 
            self::$insertQueue->queueInsert($row);
        }
        public static function commitRowInsert() {
            if (self::$insertQueue == null) return false;
            $result = self::$insertQueue->commitInsert();
            self::$insertQueue = null;
            return $result;
        }
    }
}

?>