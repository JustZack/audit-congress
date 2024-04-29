<?php

namespace API {
    class Member extends RouteGroup {
        private function __construct() {
            parent::__construct("member", "\API\MemberRoute");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Member();
            return self::$memberInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class MemberRoute extends Route { }
}

?>