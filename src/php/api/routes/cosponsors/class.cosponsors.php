<?php

namespace API {
    class Cosponsors extends RouteGroup {
        public function __construct() {
            parent::__construct("cosponsors", "\API\CosponsorsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CosponsorsRoute extends Route { }
}

?>