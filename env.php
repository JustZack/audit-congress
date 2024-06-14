<?php

namespace AuditCongress {
    class Enviroment {
        static function getUrl() {
            $modeIsProd = false;
            if ($modeIsProd) return "http://localhost/audit-congress/";
            else return "http://localhost/audit-congress/";
        }
        
        static function getConfig() {
            return \Util\File::readJSONFile(AUDITCONGRESS_CONFIG);
        }

        static function getCacheSettings() {
            return \Util\File::readJSONFile(AUDITCONGRESS_CACHE_SETTINGS);
        }
        
        private static function readSchema($pathToSchema, $schemaClass) {
            $json = \Util\File::readJSONFile($pathToSchema);
            return new $schemaClass($json);
        }

        private static ?\MySqlConnector\Schema $dbSchema = null;
        static function getDatabaseSchema() {
            if (self::$dbSchema == null) {
                self::$dbSchema = self::readSchema(AUDITCONGRESS_DB_SCHEMA, "\MySqlConnector\Schema");
            }
            return self::$dbSchema;
        }

        private static ?\API\Schema $apiSchema = null;
        static function getAPISchema() {
            if (self::$apiSchema == null) {
                self::$apiSchema = self::readSchema(AUDITCONGRESS_API_SCHEMA, "\API\Schema");
            }
            return self::$apiSchema;
        }
    }
}


?>