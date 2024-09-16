<?php

namespace APIService {

    use MySqlConnector\Comparison;
    use MySqlConnector\Condition;

    class LogsQuery extends \MySqlConnector\QueryWrapper {
        public function __construct() {
            parent::__construct("ExternalAPITokenLog");
        }

        private static function setQueryStartEnd($logs, $start = null, $end = null) {
            if ($start != null) $logs->addSearch("when", Comparison::GREATER_THAN_EQUALS, $start);
            if ($end != null) $logs->addSearch("when", Comparison::LESS_THAN_EQUALS, $end);
            return $logs;
        }
 
        public static function getLogs($tokenId = null, $url = null, $body = null, $start = null, $end = null) {
            $logs = new LogsQuery();
            $logs->setSelectColumns(["*"]);
            if ($tokenId != null) $logs->addSearch("tokenId", Comparison::EQUALS, $tokenId);
            if ($url != null) $logs->addSearch("url", Comparison::LIKE, $url);
            if ($body != null) $logs->addSearch("body", Comparison::LIKE, $body);
            $logs = self::setQueryStartEnd($logs, $start, $end);
            return $logs->selectFromDB()->fetchAllAssoc();
        }

        public static function getByService($service = null, $start = null, $end = null) {
            $logs = new LogsQuery();
            $logs->setSelectColumns(["*"]);
            if ($service != null) $logs->addSearch("service", Comparison::EQUALS, $service);
            $logs = self::setQueryStartEnd($logs, $start, $end);
            return $logs->selectFromDB()->fetchAllAssoc();
        }

        public static function insertLog($tokenId, $url, $body = "") {
            $when = \Util\Time::getNowDateTimeStr();
            $logs = new LogsQuery();
            $logs->setColumns(["when", "tokenId", "url", "body"]);
            $logs->setValues([$when, $tokenId, $url, $body]);
            $logs->insertIntoDB();
            return self::getLogs($tokenId, $url);
        }

        public static function deleteLogs($start = null, $end = null) {
            $logs = new LogsQuery();
            $logs = self::setQueryStartEnd($logs, $start, $end);
            return $logs->deleteFromDb();
        }
    }
}

?>