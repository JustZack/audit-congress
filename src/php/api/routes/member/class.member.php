<?php

namespace API {
    class Member extends RouteGroup {
        private function __construct() {
            parent::__construct("member");
        }

        private $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Member();
            return self::$memberInstance;
        }

        public function canRunAny($parameters) {

        }

        public function fetchResult($parameters) {
            
        }
    }
}

?>