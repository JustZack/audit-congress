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

        static function getSchemaPath() {
            return AUDITCONGRESS_DB_SCHEMA;
        }
    }
}


?>