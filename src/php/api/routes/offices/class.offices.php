<?php

namespace API {
    class Offices extends RouteGroup {
        private function __construct() {
            parent::__construct("offices", "\API\OfficesRoute");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Offices();
            return self::$memberInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class OfficesRoute extends Route { }
}

?>