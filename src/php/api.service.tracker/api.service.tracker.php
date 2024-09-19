<?php

namespace APIService {
    class Tracker {        
        private $apiRow = null;
        
        public function __construct($service) {
            //Todo: Write as object
        }

        public static function getUsableToken($service) {
            //1. Does this service exist in the limits and tokens table?
            $limits = self::getLimits($service);
            if ($limits == null) throw new \Exception("ApiService: No limits defined for service: `$service`");
            //2. Count rows in log for each API key of service younger than ExternalAPILimits.hoursLimit
            //  compute the relevent range of logs we need to pull
            $serviceOffset = $limits["hoursInterval"] * 60 * 60;
            $start = \Util\Time::getDateTimeStr(time() - $serviceOffset);
            $end = \Util\Time::getNowDateTimeStr();
            $countedLogsPerToken = self::countLogs($service, null, $start, $end);
            if (count($countedLogsPerToken) > 0) {
                //3. Use row with lowest usage. Counted logs are always returned smallest to largest
                $lowestUseRow = $countedLogsPerToken[0];
                //4. Ensure this usage is less than ExternalAPILimits.(limit - threshhold)
                if ($lowestUseRow["count"] >= $limits["limit"] + $limits["threshold"]) 
                    throw new \Exception("ApiService: No tokens available for service: `$service`");
                //5. Return key
                $token = self::getToken($lowestUseRow["tokenId"]);
            }
            else {
                //3 Get the first token for this service if one exists.
                $tokens = self::getTokens($service);
                if (count($tokens) > 0) $token = $tokens[0];
                else throw new \Exception("ApiService: No tokens exist for service: `$service`");
            }
            return $token;
        }



        public static function getLimits($service = null) {
            return LimitsQuery::getServiceLimits($service);
        }
        public static function addLimit($service, $limit, $threshold, $hoursInterval) {
            $existingLimit = LimitsQuery::getServiceLimits($service);
            if ($existingLimit != null) return $existingLimit;
            else {
                LimitsQuery::insertService($service, $limit, $threshold, $hoursInterval);
                return self::getLimits($service);
            }
        }
        public static function updateLimit($service, $limit = null, $threshold = null, $hoursInterval = null) {
            return LimitsQuery::updateService($service, $limit, $threshold, $hoursInterval);
        }
        public static function deleteLimit($service) {
            return LimitsQuery::deleteService($service);
        }



        public static function getTokens($service = null, $isActive = true) {
            return TokensQuery::getTokens($service, $isActive);
        }
        public static function getToken($id, $service = null, $token = null) {
            return TokensQuery::getToken($id, $service, $token);
        }
        public static function addToken($service, $token) {
            $existingToken = self::getToken(null, $service, $token);
            if ($existingToken != null) return $existingToken;
            else {
                TokensQuery::insertToken($service, $token, true);
                return self::getToken(null, $service, $token);
            }
        }
        public static function updateToken($id, $service = null, $token = null, $isActive = null) {
            return TokensQuery::updateToken($id, $service, $token, $isActive);
        }
        public static function deleteToken($id) {
            return TokensQuery::deleteToken($id);
        }



        public static function getLogs($service = null, $id = null, $url = null, $body = null, $start = null, $end = null) {
            return LogsQuery::getLogs($service, $id, $url, $body, $start, $end);
        }
        public static function addLog($id, $url, $body = null) {
            return LogsQuery::insertLog($id, $url, $body);
        }
        public static function deleteLogs($start = null, $end = null) {
            return LogsQuery::deleteLogs($start, $end);
        }
        public static function countLogs($service = null, $tokenId = null, $start = null, $end = null) {
            return LogsQuery::countLogs($service, $tokenId, $start, $end);
        }

    }
}

?>