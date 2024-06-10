<?php

namespace API {
    class Actions extends RouteGroup {
        public function __construct() {
            parent::__construct("actions", "\API\ActionsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ActionsRoute extends Route { }
}

?>