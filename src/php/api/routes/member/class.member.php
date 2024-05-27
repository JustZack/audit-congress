<?php

namespace API {
    class Member extends RouteGroup {
        public function __construct() {
            parent::__construct("members", "\API\MemberRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class MemberRoute extends Route { }
}

?>