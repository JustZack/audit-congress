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

        public function fetch_all() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_all();
            else return $this->success();
        }
        public function fetch_array() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_array();
            else return $this->success();
        }
        public function fetch_assoc() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_assoc();
            else return $this->success();
        }
        public function fetch_row() {
            if ($this->hasResults()) return $this->mysqli_result->fetch_row();
            else return $this->success();
        }
        public function fetch_column($column_num) {
            if ($this->hasResults()) return $this->mysqli_result->fetch_column($column_num);
            else return $this->success();
        }
    }
}

?>