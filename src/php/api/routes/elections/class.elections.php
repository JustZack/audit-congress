<?php

namespace API {
    class Elections extends RouteGroup {
        private function __construct() {
            parent::__construct("elections", "\API\ElectionsRoute");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Elections();
            return self::$memberInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ElectionsRoute extends Route { }
}

?>