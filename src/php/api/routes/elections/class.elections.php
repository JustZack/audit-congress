<?php

namespace API {
    class Elections extends RouteGroup {
        public function __construct() {
            parent::__construct("elections", "\API\ElectionsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ElectionsRoute extends Route { 
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\MemberElections", $functionName, $parameters);
        }
    }
    class ElectionsByBioguideId extends ElectionsRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["id"]);
        }
    }
    class ElectionsByFecId extends ElectionsRoute {
        public function __construct() {
            parent::__construct("getByFecId", ["fecid"]);
        }
    }
}

?>