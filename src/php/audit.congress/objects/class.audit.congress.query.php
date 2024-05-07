<?php

namespace AuditCongress {
    class AuditCongressQuery extends \MySqlConnector\SqlObject {
        protected static function runAdvancedQuery($sql) : \MySqlConnector\Result {
            return \MySqlConnector\Query::getResult($sql);
        }

        protected function applyPagination() {
            $pagination = \API\Runner::getPagination();
            $this->setLimit($pagination->pageSize());
            $this->setOffset($pagination->offset());
        }
    }
}

?>