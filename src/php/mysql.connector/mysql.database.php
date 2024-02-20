<?php

namespace MySqlConnector {

    class Database {
        private $databaseExists = null;
        public $name;
        


        public function __construct($databaseName) {
            $this->name = $databaseName;
        }



        //Check if this database exists
        public function exists($useCache = true) {
            $sql = "SHOW DATABASES LIKE '$this->name'";
            if ($this->databaseExists == null || !$useCache) {
                $results = Query::runQuery($sql);
                $this->databaseExists = count($results) == 1;
            }
            return $this->databaseExists;
        }        
        //List accessible databases
        public static function listDatabases() {
            $sql = "SHOW DATABASES";
            return Query::runQuery($sql);
        }



        //Create this database
        public function create() {
            $sql = "CREATE DATABASE $this->name";
            $this->databaseExists = null;
            return Query::runActionQuery($sql);
        }
        //Drop this database
        public function drop() {
            $sql = "DROP SCHEMA $this->name";
            $this->databaseExists = null;
            return Query::runActionQuery($sql);
        }
    } 
}

?>