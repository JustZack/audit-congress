<?php

namespace API {
    class Session extends RouteGroup {
        public function __construct() {
            parent::__construct("session", "\API\SessionRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SessionRoute extends Route { }
}

?>