<?php

namespace APIService {

    use MySqlConnector\Comparison;
    use MySqlConnector\Condition;
    use MySqlConnector\ConditionGroup;

    class LogsQuery extends \MySqlConnector\QueryWrapper {
        public function __construct() {
            parent::__construct("ExternalAPITokenLog");
        }

        private static function setQueryStartEnd($logs, $start = null, $end = null) {
            if ($start != null) $logs->addSearch("when", Comparison::GREATER_THAN_EQUALS, $start);
            if ($end != null) $logs->addSearch("when", Comparison::LESS_THAN_EQUALS, $end);
            return $logs;
        }
 
        private static function buildGetLogsQuery($service = null, $tokenId = null, $url = null, $body = null, $start = null, $end = null) {
            $logs = new LogsQuery();
            if ($service != null) $logs->addSearch("service", Comparison::EQUALS, $service);
            if ($tokenId != null) $logs->addSearch("tokenId", Comparison::EQUALS, $tokenId);
            if ($url != null) $logs->addSearch("url", Comparison::LIKE, $url);
            if ($body != null) $logs->addSearch("body", Comparison::LIKE, $body);
            $logs = self::setQueryStartEnd($logs, $start, $end);

            $joinGroup = new ConditionGroup();
            $joinGroup->addCondition(new Condition("ExternalAPITokens.id", Comparison::EQUALS, "ExternalAPITokenLog.tokenId", true));
            $logs->addJoin("ExternalAPITokens", $joinGroup);
            return $logs;
        }

        public static function getLogs($service = null, $tokenId = null, $url = null, $body = null, $start = null, $end = null) {
            $logs = self::buildGetLogsQuery($service, $tokenId, $url, $body, $start, $end);
            $logs->setSelectColumns(["ExternalAPITokenLog.id", "`when`", "tokenId", "ExternalAPITokens.service", "url", "body"]);
            return $logs->selectFromDB()->fetchAllAssoc();
        }

        public static function insertLog($tokenId, $url, $body = "") {
            $when = \Util\Time::getNowDateTimeStr();
            $logs = new LogsQuery();
            $logs->setColumns(["when", "tokenId", "url", "body"]);
            $logs->setValues([$when, $tokenId, $url, $body]);
            return $logs->insertIntoDB();
        }

        public static function deleteLogs($start = null, $end = null) {
            $logs = new LogsQuery();
            $logs = self::setQueryStartEnd($logs, $start, $end);
            return $logs->deleteFromDb();
        }

        public static function countLogs($service = null, $tokenId = null,$start = null, $end = null) {
            $logs = self::buildGetLogsQuery($service, $tokenId, null, null, $start, $end);
            $logs->setSelectColumns(["count(*) as count", "tokenId", "ExternalAPITokens.service"]);
            $logs->setOrderBy(["count"], true);
            $logs->setGroupBy(["tokenId", "service"]);
            return $logs->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>