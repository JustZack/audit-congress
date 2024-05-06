<?php

namespace API {
    class Bills extends RouteGroup {
        private function __construct() {
            parent::__construct("bills", "\API\BillsRoute");
        }

        private static $billInstance = null;
        public static function getInstance() {
            if (self::$billInstance == null) self::$billInstance = new \API\Bills();
            return self::$billInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class BillsRoute extends Route { }
}

?>