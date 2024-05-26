<?php

namespace API {
    class Congress extends RouteGroup {
        private function __construct() {
            parent::__construct("congress", "\API\CongressRoute");
        }

        private static $congressInstance = null;
        public static function getInstance() {
            if (self::$congressInstance == null) self::$congressInstance = new \API\Congress();
            return self::$congressInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CongressRoute extends Route { }
}

?>