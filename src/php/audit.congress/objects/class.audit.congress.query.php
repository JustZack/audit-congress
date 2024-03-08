<?php

namespace AuditCongress {

    class AuditCongressQuery extends \MySqlConnector\SqlObject {
        protected static function runAdvancedQuery($sql) : \MySqlConnector\Result {
            return \MySqlConnector\Query::getResult($sql);
        }
    }
}

?>