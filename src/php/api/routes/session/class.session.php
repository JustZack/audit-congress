<?php

namespace API {
    class Session extends RouteGroup {
        private function __construct() {
            parent::__construct("session", "\API\SessionRoute");
        }

        private static $sessionInstance = null;
        public static function getInstance() {
            if (self::$sessionInstance == null) self::$sessionInstance = new \API\Session();
            return self::$sessionInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SessionRoute extends Route { }
}

?>