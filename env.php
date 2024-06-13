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
        
        private static ?\MySqlConnector\Schema $schema = null;
        static function getDatabaseSchema() {
            if (self::$schema == null) {
                $json = \Util\File::readJSONFile(AUDITCONGRESS_DB_SCHEMA);
                self::$schema = new \MySqlConnector\Schema($json);
            }
            return self::$schema;
        }
    }
}


?>