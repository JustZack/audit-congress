<?php

namespace API {
    class Bills extends RouteGroup {
        public function __construct() {
            parent::__construct("bills", "\API\BillsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class BillsRoute extends Route { 
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\Bills", $functionName, $parameters);
        }
    }
    class BillsById extends BillsRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }
    class BillsBySponsorId extends BillsRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["bioguideId"]);
        }
    }
    class BillsByFilter extends BillsRoute {
        public function __construct() {
            parent::__construct("getByFilter", []);
        }
        //Note: No required parameters        
        public function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $title = Parameters::get("title");
            $sort = Parameters::getArray("sort");

            if ($sort == null) return $this->getCallableFunction()($congress, $type, $number, $title);
            else               return $this->getCallableFunction()($congress, $type, $number, $title, $sort);
        }
    }
}

?>