<?php

namespace AuditCongress {
    abstract class AuditCongressQuery extends \MySqlConnector\SqlObject {
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
}

?>