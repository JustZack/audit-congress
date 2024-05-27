<?php

namespace API {
    class Elections extends RouteGroup {
        public function __construct() {
            parent::__construct("elections", "\API\ElectionsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ElectionsRoute extends Route { }
}

?>