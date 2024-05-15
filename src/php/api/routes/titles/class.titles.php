<?php

namespace API {
    class Titles extends RouteGroup {
        private function __construct() {
            parent::__construct("titles", "\API\TitlesRoute");
        }

        private static $cosponsorsInstance = null;
        public static function getInstance() {
            if (self::$cosponsorsInstance == null) self::$cosponsorsInstance = new \API\Titles();
            return self::$cosponsorsInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class TitlesRoute extends Route { }
}

?>