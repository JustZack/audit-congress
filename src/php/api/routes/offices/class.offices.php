<?php

namespace API {
    class Offices extends RouteGroup {
        public function __construct() {
            parent::__construct("offices", "\API\OfficesRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class OfficesRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\MemberOffices", $functionName, $parameters);
        }
    }

    class OfficesByOfficeId extends OfficesRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }

    class OfficesByBioguideId extends OfficesRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["bioguideId"]);
        }
    }
}

?>