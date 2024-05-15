<?php

namespace API {
    class Subjects extends RouteGroup {
        private function __construct() {
            parent::__construct("subjects", "\API\SubjectsRoute");
        }

        private static $subjectsInstance = null;
        public static function getInstance() {
            if (self::$subjectsInstance == null) self::$subjectsInstance = new \API\Subjects();
            return self::$subjectsInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SubjectsRoute extends Route { }
}

?>