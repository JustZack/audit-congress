<?php

namespace MySqlConnector {
    class Result {
        private $mysqli_result;
        
        public function __construct($mysqli_result) {
            $this->mysqli_result = $mysqli_result;
        }

        public function success() {
            return !$this->failure();
        }
        public function failure() {
            return $this->mysqli_result == false;
        }
        public function isEmptyResult() {
            return $this->success() && $this->mysqli_result == true;
        }
        public function hasResults() {
            return gettype($this->mysqli_result) == "object";
        }

        public function fetchAll() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_all();
            else return $this->success();
        }
        public function fetchArray() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_array();
            else return $this->success();
        }
        public function fetchAssoc() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_assoc();
            else return $this->success();
        }
        public function fetchRow() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_row();
            else return $this->success();
        }
        public function fetchColumn($column_num) {
            if ($this->hasResults()) return $this->mysqli_result->fetch_column($column_num);
            else return $this->success();
        }
    }
}

?>