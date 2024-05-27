<?php

namespace API {
    class Terms extends RouteGroup {
        public function __construct() {
            parent::__construct("terms", "\API\TermsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class TermsRoute extends Route { }
}

?>