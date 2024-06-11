<?php

namespace API {
    class BioguideToThomas extends RouteGroup {
        public function __construct() {
            parent::__construct("bioguideToThomas", "\API\BioguideToThomasRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class BioguideToThomasRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\Members", $functionName, $parameters);
        }
    }
    class BioguideToThomasSingle extends BioguideToThomasRoute {
        public function __construct() {
            parent::__construct("getBioguideToThomasIdMapping", []);
        }
    }
}

?>