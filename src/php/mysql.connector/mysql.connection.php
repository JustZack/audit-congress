<?php

namespace MySqlConnector {
    class ConnectionDeconstructor {
        public function __destruct() {
            Connection::close();
        }
    }
    class Connection {
        private static ConnectionDeconstructor $destructor;
        private static $config = false;
        private static $connection = false;
        private static $database = "";
        
        public static function getConfig() {
            if (Connection::$config == false) 
                Connection::$config = \AuditCongress\Enviroment::getConfig();
            return Connection::$config;            
        }

        public static function getConnection() : \mysqli {
            if (Connection::$connection == false) {
                $config = Connection::getConfig();
                
                $url = $config["dburl"];
                $user = $config["dbuser"];
                $password = $config["dbpassword"];
                $database = isset($config["db"]) ? $config["db"] : null;

                Connection::open($url, $user, $password, $database);
                Connection::$destructor = new ConnectionDeconstructor();
            }
            return Connection::$connection;
        }

        public static function open($url, $user, $password, $database = null) {           
            Connection::$connection = new \mysqli($url, $user, $password);
            if (Connection::$connection->connect_errno) {
                throw new SqlException("Failed to connect to ".$url." with information provided in config.");
            } else if (isset($database)) {
                Connection::useDatabase($database);
            }
        }

        public static function isOpen() {
            return gettype(Connection::$connection) == "object";
        }

        public static function close() {
            if (Connection::isOpen()) {
                $connection = Connection::getConnection();
                $connection->close();
                Connection::$connection = false;
            }
        }

        public static function getDatabase() {
            if (!Connection::isOpen()) $connection = Connection::getConnection();
            return Connection::$database;
        }
        public static function useDatabase($database) {
            if (Connection::isOpen()) { 
                $connection = Connection::getConnection();
                Connection::$database = $database;
                $connection->select_db(Connection::$database);
            }
        }

        public static function lastError() {
            if (Connection::isOpen()) {
                $connection = Connection::getConnection();
                return $connection->error;
            }
            else return "";
        }
    }
}

?>