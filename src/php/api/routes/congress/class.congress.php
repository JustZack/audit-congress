<?php

namespace API {
    class Congress extends RouteGroup {
        public function __construct() {
            parent::__construct("congress", "\API\CongressRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CongressRoute extends Route { }
}

?>