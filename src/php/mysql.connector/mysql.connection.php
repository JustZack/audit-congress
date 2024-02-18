<?php

namespace MySqlConnector {

    class Connection {
        private static $config = false;
        public static function getConfig() {
            if (Connection::$config == false) 
                Connection::$config = \AuditCongress\Enviroment::getConfig();
            return Connection::$config;            
        }

        private static $connection = false;
        public static function getConnection() {
            if (Connection::$connection == false) {
                $config = Connection::getConfig();
                
                $url = $config["dburl"];
                $user = $config["dbuser"];
                $password = $config["dbpassword"];

                Connection::$connection = new \mysqli($url, $user, $password);
                if (Connection::$connection->connect_errno) {
                    throw new \Exception("Failed to connect to ".$url." with information provided in config.");
                }
            }
            return Connection::$connection;
        }
    }
}

?>