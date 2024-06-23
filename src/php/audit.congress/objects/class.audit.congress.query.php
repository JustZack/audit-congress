<?php

namespace AuditCongress {
    abstract class AuditCongressQuery extends \MySqlConnector\QueryWrapper {
        protected static function runAdvancedQuery($sql) : \MySqlConnector\Result {
            return \MySqlConnector\Query::getResult($sql);
        }

        protected static function getWithSearchSelect($column, $equalityOperator, $value) {
            //Anything that implements this is meant to override the constructor.
            $theQuery = new static();
            $theQuery->addSearchValue($column, $equalityOperator, $value);
            return $theQuery;
        }

        protected abstract function applyDefaultOrder();

        protected function applyPagination() {
            $pagination = \API\Runner::getPagination();
            $this->setLimit($pagination->pageSize());
            $this->setOffset($pagination->offset());
        }
    }

    trait GetByBioguideIdQuery {
        public static function getByBioguideId($bioguideId) {
            $query = self::getWithSearchSelect("bioguideId", "=", $bioguideId);
            $query->applyDefaultOrder();
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }

    trait GetByIdQuery {
        public static function getById($id) {
            $query = self::getWithSearchSelect("id", "=", $id);
            $query->applyDefaultOrder();
            $query->applyPagination();
            return $query->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>