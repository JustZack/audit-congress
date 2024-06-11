<?php

namespace API {
    class Congress extends RouteGroup {
        public function __construct() {
            parent::__construct("congress", "\API\CongressRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CongressRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\Congresses", $functionName, $parameters);
        }
    }
    class CongressByNumber extends CongressRoute {
        public function __construct() {
            parent::__construct("getByNumber", ["number"]);
        }
    }
    class CongressByYear extends CongressRoute {
        public function __construct() {
            parent::__construct("getByYear", ["year"]);
        }
    }
    class CurrentCongress extends CongressRoute {
        public function __construct() {
            parent::__construct("getCurrent", ["current"]);
        }
    }
    class AllCongresses extends CongressRoute {
        public function __construct() {
            parent::__construct("getAll", []);
        }
    }
}

?>