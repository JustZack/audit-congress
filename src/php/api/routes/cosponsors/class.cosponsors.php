<?php

namespace API {
    class Cosponsors extends RouteGroup {
        private function __construct() {
            parent::__construct("cosponsors", "\API\CosponsorsRoute");
        }

        private static $cosponsorsInstance = null;
        public static function getInstance() {
            if (self::$cosponsorsInstance == null) self::$cosponsorsInstance = new \API\Cosponsors();
            return self::$cosponsorsInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CosponsorsRoute extends Route { }
}

?>