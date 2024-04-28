<?php

namespace API {
    class Member extends RouteGroup {
        private function __construct() {
            parent::__construct("member");
        }

        private static $memberInstance = null;
        public static function getInstance() {
            if (self::$memberInstance == null) self::$memberInstance = new \API\Member();
            return self::$memberInstance;
        }

        public static function fetchRouteClassNames() {
            return ["MemberByBioguideId", "MemberByAnyName"];
        }

    }
}

?>