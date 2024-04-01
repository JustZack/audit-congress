<?php

namespace AuditCongress {
    class Enviroment {
        static function getUrl() {
            $modeIsProd = false;
            if ($modeIsProd) return "http://localhost/audit-congress/";
            else return "http://localhost/audit-congress/";
        }
        
        static function getConfig() {
            $configFile = file_get_contents(AUDITCONGRESS_CONFIG);
            $config = explode("\r\n",$configFile);

            $settings = array();
            foreach ($config as $setting) {
                //Only process valid settings like: a;=;b
                if (strlen($setting) >= 5) {
                    $parts = explode(";=;", $setting);
                    $settings[$parts[0]] = $parts[1];
                }
            }
            return $settings;
        }

        static function getCacheSettings() {
            $settings = json_decode(file_get_contents(AUDITCONGRESS_CACHE_SETTINGS), true);
            return $settings;
        }

        static function getSchemaPath() {
            return AUDITCONGRESS_DB_SCHEMA;
        }
    }
}


?>