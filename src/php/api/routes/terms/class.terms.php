<?php

namespace API {
    class Terms extends RouteGroup {
        private function __construct() {
            parent::__construct("terms", "\API\TermsRoute");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Terms();
            return self::$memberInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class TermsRoute extends Route { }
}

?>