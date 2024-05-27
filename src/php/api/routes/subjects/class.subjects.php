<?php

namespace API {
    class Subjects extends RouteGroup {
        public function __construct() {
            parent::__construct("subjects", "\API\SubjectsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SubjectsRoute extends Route { }
}

?>