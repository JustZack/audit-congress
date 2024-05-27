<?php

namespace API {
    class Titles extends RouteGroup {
        public function __construct() {
            parent::__construct("titles", "\API\TitlesRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class TitlesRoute extends Route { }
}

?>