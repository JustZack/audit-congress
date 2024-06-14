<?php

namespace AuditCongress {
    class Environment {
        public static function getUrl() {
            $modeIsProd = false;
            if ($modeIsProd) return "http://localhost/audit-congress/";
            else return "http://localhost/audit-congress/";
        }
        
        public static function getConfig() {
            return \Util\File::readJSONFile(AUDITCONGRESS_CONFIG);
        }

        public static function getCacheSettings() {
            return \Util\File::readJSONFile(AUDITCONGRESS_CACHE_SETTINGS);
        }
        
        private static function readSchema($pathToSchema, $schemaClass) {
            $json = \Util\File::readJSONFile($pathToSchema);
            return new $schemaClass($json);
        }

        private static ?\MySqlConnector\Schema $dbSchema = null;
        public static function getDatabaseSchema() {
            if (self::$dbSchema == null) {
                self::$dbSchema = self::readSchema(AUDITCONGRESS_DB_SCHEMA, "\MySqlConnector\Schema");
            }
            return self::$dbSchema;
        }

        public static function enforceDatabaseSchema() {
            $schema = self::getDatabaseSchema();
            $enforcer = new \MySqlConnector\SchemaEnforcer($schema);
            $enforcer->enforceSchema();
            $operations = $enforcer::getDBOperationsList();
            return array("valid" => true, "operations" => $operations);
        }


        private static ?\API\Schema $apiSchema = null;
        public static function getAPISchema() {
            if (self::$apiSchema == null) {
                self::$apiSchema = self::readSchema(AUDITCONGRESS_API_SCHEMA, "\API\Schema");
            }
            return self::$apiSchema;
        }
    }
}


?>