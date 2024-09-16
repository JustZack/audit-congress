<?php

namespace APIService {
    class Tracker {        
        private $apiRow = null;
        
        public function __construct($service) {
            //Todo: Write as object
        }

        public static function getUsableKey($service) {
            //Todo: write this
            //1. Does this service exist in the limits and tokens table?

            //2. Count rows in log for each API key of service younger than ExternalAPILimits.hoursLimit

            //3. Use row with lowest usage

            //4. Ensure this usage is less than ExternalAPILimits.(limit - threshhold)

            //5. Return key
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



        public static function getLogs($id = null, $url = null, $start = null, $end = null) {
            return LogsQuery::getLogs($id, $url, $start, $end);
        }
        public static function addLog($id, $url, $body = null) {
            return LogsQuery::insertLog($id, $url, $body);
        }
        public static function deleteLogs($start = null, $end = null) {
            return LogsQuery::deleteLogs($start, $end);
        }

    }
}

?>