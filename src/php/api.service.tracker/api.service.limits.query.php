<?php

namespace APIService {

    use MySqlConnector\Comparison;
    use MySqlConnector\Condition;

    class LimitsQuery extends \MySqlConnector\QueryWrapper {
        public function __construct() {
            parent::__construct("ExternalAPILimits");
        }

        public static function getServiceLimits($service = null) {
            $cache = new LimitsQuery();
            if ($service != null) $cache->addSearch("service", Comparison::EQUALS, $service);
            $result = $cache->selectFromDB()->fetchAllAssoc();
            //Return all service limits if one wasn't given
            if ($service == null) return $result;
            //Otherwise, return the first service if one was given
            else if (count($result) > 0) return $result[0];
            //Or return null when a service was given and none were found
            else return null;
        }

        private static function getSetColsAndVals($service, $limit, $threshold, $hoursInterval, $added, $updated) {
            $colsToSet = []; $valsToSet = [];
            if ($service != null) {
                array_push($colsToSet, "service"); array_push($valsToSet, $service);
            }
            if ($limit != null) {
                array_push($colsToSet, "limit"); array_push($valsToSet, $limit);
            }
            if ($threshold != null) {
                array_push($colsToSet, "threshold"); array_push($valsToSet, $threshold);
            }
            if ($hoursInterval != null) {
                array_push($colsToSet, "hoursInterval"); array_push($valsToSet, $hoursInterval);
            }
            if ($added != null) {
                array_push($colsToSet, "added"); array_push($valsToSet, $added);
            }
            if ($updated != null) {
                array_push($colsToSet, "updated"); array_push($valsToSet, $updated);
            }
            return ["columns" => $colsToSet, "values" => $valsToSet];
        }

        private static function getInsertOrUpdateQuery($service, $limit, $threshold, $hoursInterval, $added, $updated) {
            $setItems = self::getSetColsAndVals($service, $limit, $threshold, $hoursInterval, $added, $updated);
            list("columns" => $colsToSet, "values" => $valsToSet) = $setItems;

            $cache = new LimitsQuery();
            $cache->setColumns($colsToSet);
            $cache->setValues($valsToSet);
            return $cache;
        }

        public static function insertService($service, $limit, $threshold, $hoursInterval) {
            $existing = self::getServiceLimits($service);
            if ($existing != null) return false;

            $added = \Util\Time::getNowDateTimeStr();
            $cache = self::getInsertOrUpdateQuery($service, $limit, $threshold, $hoursInterval, $added, $added);
            return $cache->insertIntoDB();
        }

        public static function updateService($service, $limit = null, $threshold = null, $hoursInterval = null) {
            $existing = self::getServiceLimits($service);
            if ($existing == null) return false;

            $updated = \Util\Time::getNowDateTimeStr();
            $cache = self::getInsertOrUpdateQuery(null, $limit, $threshold, $hoursInterval, null, $updated);
            $cache->addSearch("service", Comparison::EQUALS, $service);
            $cache->updateInDb();
            return self::getServiceLimits($service);
        }

        public static function deleteService($service) {
            $cache = new LimitsQuery();
            $cache->addSearch("service", Comparison::EQUALS, $service);
            return $cache->deleteFromDb();
        }
    }
}

?>