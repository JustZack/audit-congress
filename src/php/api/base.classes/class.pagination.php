<?php

namespace API {
    class Pagination {
        private 
            $pageNumber,
            $pageSize;
        
        private static $defaultPageSize = 25;

        private static function enforcePageNumberBounds($pageNumber) {
            if ($pageNumber == null || $pageNumber < 1) throw new \OutOfBoundsException("\API\Pagination: pageNumber must be an integer > 0.");
        }
        private static function enforcePageSizeBounds($pageSize) {
            if ($pageSize < 1) throw new \OutOfBoundsException("\API\Pagination: pageSize must be an integer > 0.");
        }
        private static function enforceOffsetBounds($offset) {
            if ($offset < 0) throw new \OutOfBoundsException("\API\Pagination: offset must be an integer >= 0.");
        }

        private function convertNullPageSize($pageSize) {
            return $pageSize == null ? self::$defaultPageSize : $pageSize; 
        }
        private function convertNullPage($page) {
            return $page == null ? 1 : $page; 
        }

        public function __construct($pageNumber = 1, $pageSize = 25) {
            $pageNumber = self::convertNullPage($pageNumber);
            $pageSize = self::convertNullPageSize($pageSize);

            self::enforcePageNumberBounds($pageNumber);
            self::enforcePageSizeBounds($pageSize);

            $this->pageNumber = $pageNumber;
            $this->pageSize = $pageSize;
        }

        public static function getFromOffset($offset, $pageSize = 25) {
            $pageSize = self::convertNullPageSize($pageSize);
            
            self::enforceOffsetBounds($offset);
            
            $number = floor((1.0 * $offset)/$pageSize)+1;
            return new Pagination($number, $pageSize);
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