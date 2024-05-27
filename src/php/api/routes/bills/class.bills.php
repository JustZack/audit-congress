<?php

namespace API {
    class Bills extends RouteGroup {
        public function __construct() {
            parent::__construct("bills", "\API\BillsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class BillsRoute extends Route { }
}

?>