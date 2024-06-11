<?php

namespace API {
    class Titles extends RouteGroup {
        public function __construct() {
            parent::__construct("titles", "\API\TitlesRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class TitlesRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\BillTitles", $functionName, $parameters);
        }
    }
    class TitlesById extends TitlesRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }
    class TitlesByBillId extends TitlesRoute {
        public function __construct() {
            parent::__construct("getByBillId", ["billId"]);
        }
    }
}

?>