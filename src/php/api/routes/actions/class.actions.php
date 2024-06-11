<?php

namespace API {
    class Actions extends RouteGroup {
        public function __construct() {
            parent::__construct("actions", "\API\ActionsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ActionsRoute extends Route { 
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\BillActions", $functionName, $parameters);
        }
    }
    class ActionsById extends ActionsRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }
    class ActionsByBillId extends ActionsRoute {
        public function __construct() {
            parent::__construct("getByBillId", ["billId"]);
        }
    }
}

?>