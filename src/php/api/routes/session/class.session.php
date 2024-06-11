<?php

namespace API {
    class Session extends RouteGroup {
        public function __construct() {
            parent::__construct("session", "\API\SessionRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SessionRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\Sessions", $functionName, $parameters);
        }
    }
    class SessionsByCongress extends SessionRoute {
        public function __construct() {
            parent::__construct("getByCongress", ["congress"]);
        }
    }
    class SessionsByCongressAndNumber extends SessionRoute {
        public function __construct() {
            parent::__construct("getByCongressAndNumber", ["congress", "number"]);
        }
    }
    class SessionsByCongressAndChamber extends SessionRoute {
        public function __construct() {
            parent::__construct("getByCongressAndChamber", ["congress", "chamber"]);
        }
    }
    class SessionsByCongressNumberAndChamber extends SessionRoute {
        public function __construct() {
            parent::__construct("getByCongressNumberAndChamber", ["congress", "number", "chamber"]);
        }
    }
    class SessionByDate extends SessionRoute {
        public function __construct() {
            parent::__construct("getByDate", ["date"]);
        }
    }
    class CurrentSessions extends SessionRoute {
        public function __construct() {
            parent::__construct("getCurrent", ["current"]);
        }
    }
    class AllSessions extends SessionRoute {
        public function __construct() {
            parent::__construct("getAll", []);
        }
    }
}

?>