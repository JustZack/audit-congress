<?php

namespace APIService {

    use MySqlConnector\Comparison;
    use MySqlConnector\Condition;

    class TokensQuery extends \MySqlConnector\QueryWrapper {
        public function __construct() {
            parent::__construct("ExternalAPITokens");
        }

        private static function getTokenByIdQuery($id = null) {
            $tokens = new TokensQuery();
            if ($id != null) $tokens->addSearch("id", Comparison::EQUALS, $id);
            return $tokens;
        }

        public static function getToken($id = null, $service = null, $token = null) {
            if (\Util\General::allNull($id, $service, $token)) return null;
            $tokens = self::getTokenByIdQuery($id);

            if ($service != null) $tokens->addSearch("service", Comparison::EQUALS, $service);
            if ($token != null) $tokens->addSearch("token", Comparison::EQUALS, $token);

            $result = $tokens->selectFromDB()->fetchAllAssoc();
            if (count($result) > 0) return $result[0];
            else                    return null;
        }

        public static function getTokens($service = null, $isActive = null) {
            $tokens = new TokensQuery();
            if ($service == null)   $tokens->addSearch("service", Comparison::EQUALS, $service);
            if (is_bool($isActive)) $tokens->addSearch("isActive", Comparison::EQUALS, $isActive);
            return $tokens->selectFromDB()->fetchAllAssoc();
        }

        private static function getSetColsAndVals($id, $service, $token, $added, $updated, $isActive) {
            $colsToSet = []; $valsToSet = [];
            if ($id != null) {
                array_push($colsToSet, "id"); array_push($valsToSet, $id);
            }
            if ($service != null) {
                array_push($colsToSet, "service"); array_push($valsToSet, $service);
            }
            if ($token != null) {
                array_push($colsToSet, "token"); array_push($valsToSet, $token);
            }
            if ($added != null) {
                array_push($colsToSet, "added"); array_push($valsToSet, $added);
            }
            if ($updated != null) {
                array_push($colsToSet, "updated"); array_push($valsToSet, $updated);
            }
            if (is_bool($isActive)) {
                array_push($colsToSet, "isActive"); array_push($valsToSet, $isActive);
            }
            return ["columns" => $colsToSet, "values" => $valsToSet];
        }

        private static function getInsertOrUpdateQuery($id = null, $service, $token, $added, $updated, $isActive) {
            $setItems = self::getSetColsAndVals($id, $service, $token, $added, $updated, $isActive);
            list("columns" => $colsToSet, "values" => $valsToSet) = $setItems;

            $tokens = new TokensQuery();
            $tokens->setColumns($colsToSet);
            $tokens->setValues($valsToSet);
            return $tokens;
        }

        public static function insertToken($service, $token, $isActive) {
            $existing = self::getToken(null, $service, $token);
            if ($existing != null) return false;

            $added = \Util\Time::getNowDateTimeStr();
            $updated = $added;
            $tokens = self::getInsertOrUpdateQuery(null, $service, $token, $added, $updated, $isActive);
            $tokens->insertIntoDB();
            return self::getToken(null, $service, $token);
        }

        public static function updateToken($id, $service = null, $token = null, $isActive = null) {
            $existing = self::getToken($id);
            if ($existing == null) return false;
            
            $updated = \Util\Time::getNowDateTimeStr();
            $tokens = self::getInsertOrUpdateQuery($id, $service, $token, null, $updated, $isActive);
            $tokens->updateInDb();
            return self::getToken($id);
        }

        public static function deleteToken($id) {
            $tokens = self::getTokenByIdQuery($id);
            $removed = $tokens->selectFromDB()->fetchAllAssoc();
            if ($removed != null) $tokens->deleteFromDb();
            return $removed;
        }
    }
}

?>