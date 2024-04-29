<?php

namespace API {
    class Socials extends RouteGroup {
        private function __construct() {
            parent::__construct("socials", "\API\SocialsRoute");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Socials();
            return self::$memberInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SocialsRoute extends Route { }
}

?>