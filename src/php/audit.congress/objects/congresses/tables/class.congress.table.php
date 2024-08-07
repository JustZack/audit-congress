<?php 

namespace AuditCongress {

    class Congresses extends CongressTable {
        
        use \Util\GetInstance, TruncateRows, InsertQueueing;

        private function __construct() {
            parent::__construct("Congresses", "CongressQuery", "CongressRow");
        }

        private static function genericQuery($function, ...$arguments) {
            self::enforceCache();
            $result = ("\AuditCongress\CongressQuery::$function")(...$arguments);
            return self::parseResult($result);
        }

        public static function getByNumber($congressNumber) {
            $result = self::genericQuery("getByNumber", $congressNumber);
            return self::returnFirst($result);
        }

        public static function getByYear($year) { 
            $result = self::genericQuery("getByYear", $year); 
            return self::returnFirst($result);
        }

        public static function getCurrent() {
            return self::getByYear(date("Y"));
        }

        public static function getAll() { 
            return self::genericQuery("getAll"); 
        }
    }
}

?>