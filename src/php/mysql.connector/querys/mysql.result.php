<?php

namespace MySqlConnector {
    class Result {
        private $mysqli_result, $sql_formated;
        
        public function __construct($mysqli_result, $sql_formated) {
            $this->mysqli_result = $mysqli_result;
            $this->sql_formated = $sql_formated;
        }


        public function getSql() {
            return $this->sql_formated;
        }

        //True if the query was a success
        public function success() {
            return !$this->failure();
        }
        //True if the query was a failure (mysqli_result equals false)
        public function failure() {
            return $this->mysqli_result == false;
        }
        //True if the result was a success and the mysqli_result equals true
        public function isEmptyResult() {
            return $this->success() && $this->mysqli_result == true;
        }
        //True if the result is an object (Vs. boolean for success or failure)
        public function hasResults() {
            return gettype($this->mysqli_result) == "object";
        }

        //Return the result all at once, as an array
        public function fetchAll() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_all();
            else return $this->success();
        }
        
        //Return one row at a time, as an array
        public function fetchArray() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_array();
            else return $this->success();
        }
        //Return all rows, as an array of arrays
        public function fetchAllArray() {
            $rowsArray = array();
            while ($row = $this->fetchArray()) array_push($rowsArray, $row);
            return $rowsArray;
        }

        //Return one row at a time, as an associativce array
        public function fetchAssoc() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_assoc();
            else return $this->success();
        }
        //Return one row at a time, as an associativce array
        public function fetchAllAssoc() {
            $rowsAssoc = array();
            while ($row = $this->fetchAssoc()) array_push($rowsAssoc, $row);
            return $rowsAssoc;
        }

        //Return one row at a time
        public function fetchRow() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_row();
            else return $this->success();
        }
        //Return all rowsm as mysqli rows
        public function fetchAllRow() {
            $rows = array();
            while ($row = $this->fetchRow()) array_push($rows, $row);
            return $rows;
        }

        //Return a full column, as an array
        public function fetchColumn($column_num) {
            if ($this->hasResults()) return $this->mysqli_result->fetch_column($column_num);
            else return $this->success();
        }
    }
}

?>