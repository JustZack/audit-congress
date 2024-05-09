<?php

namespace API {
    class Pagination {
        private 
            $pageNumber,
            $pageSize;
        
        private static $defaultPageSize = 25;

        private static function enforceOffsetBounds($offset) {
            if ($offset < 0) throw new \OutOfBoundsException("\API\Pagination: offset must be an integer >= 0.");
        }

        private function convertBadPageSize($pageSize) {
            return ($pageSize == null || $pageSize <= 0) ? self::$defaultPageSize : $pageSize; 
        }
        private function convertBadPage($page) {
            return ($page == null || $page <= 0) ? 1 : $page; 
        }
        private function convertBadOffset($offset) {
            return ($offset == null ||   $offset < 0) ? 0 : $offset; 
        }

        public function __construct($pageNumber = 1, $pageSize = 25) {
            $pageNumber = self::convertBadPage($pageNumber);
            $pageSize = self::convertBadPageSize($pageSize);

            $this->pageNumber = $pageNumber;
            $this->pageSize = $pageSize;
        }

        public static function getFromOffset($offset, $pageSize = 25) {
            $offset = self::convertBadOffset($offset);
            $pageSize = self::convertBadPageSize($pageSize);

            $page = floor((1.0 * $offset)/$pageSize)+1;
            return new Pagination($page, $pageSize);
        }

        public static function getFromPage($pageNumber, $pageSize = 25) {
            return new Pagination($pageNumber, $pageSize);
        }

        //What page does this object represent? Non zero.
        public function page() { return $this->pageNumber; }
        //How large are these pages? Non zero.
        public function pageSize() { return $this->pageSize; }
        //What is the offset represented by this object? Could be zero.
        public function offset() { return ($this->page()-1) * $this->pageSize(); }
        //Convert the object to an assoc array
        public function toArray() {
            $array = array();
            $array["page"] = $this->page();
            $array["pageSize"] = $this->pageSize();
            $array["offset"] = $this->offset();
            return $array;
        }
    }
}

?>