<?php

namespace API {
    class Offices extends RouteGroup {
        public function __construct() {
            parent::__construct("offices", "\API\OfficesRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class OfficesRoute extends Route { }
}

?>