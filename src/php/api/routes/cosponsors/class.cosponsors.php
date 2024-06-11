<?php

namespace API {
    class Cosponsors extends RouteGroup {
        public function __construct() {
            parent::__construct("cosponsors", "\AuditCongress\BillCosponsors");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBioguideId", ["bioguideId"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new CosponsorsByFilter());
        }
    }
    class CosponsorsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillCosponsors", "getByFilter", []);
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