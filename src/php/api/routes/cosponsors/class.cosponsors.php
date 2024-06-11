<?php

namespace API {
    class Cosponsors extends RouteGroup {
        public function __construct() {
            parent::__construct("cosponsors", "\API\CosponsorsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class CosponsorsRoute extends Route { 
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\BillCosponsors", $functionName, $parameters);
        }
    }
    class CosponsorsById extends CosponsorsRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }
    class CosponsorsByBioguideId extends CosponsorsRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["bioguideId"]);
        }
    }
    class CosponsorsByBillId extends CosponsorsRoute {
        public function __construct() {
            parent::__construct("getByBillId", ["billId"]);
        }
    }
    class CosponsorsByFilter extends CosponsorsRoute {
        public function __construct() {
            parent::__construct("getByFilter", []);
        }
        //Note: No required parameters        
        public function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $bioguideId = Parameters::get("bioguideId");
            $sort = Parameters::getArray("sort");

            if ($sort == null) return $this->getCallableFunction()($congress, $type, $number, $bioguideId);
            else               return $this->getCallableFunction()($congress, $type, $number, $bioguideId, $sort);
            
        }
    }
}

?>