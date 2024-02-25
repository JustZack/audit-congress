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
        public static function showDatabases() {
            $sql = "SHOW DATABASES";
            return Query::runQuery($sql);
        }

        //List tables in the currently selection database
        public function showTables() {
            $sql = "SHOW TABLES";
            Connection::useDatabase($this->name);
            $allTableRows = Query::runQuery($sql);
            $tables = array();
            //Move the the found tables into a simple array instead of being split by row
            foreach ($allTableRows as $tableRow) array_push($tables, $tableRow[0]);
            return $tables;
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