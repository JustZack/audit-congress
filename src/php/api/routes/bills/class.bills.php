<?php

namespace API {
    class Bills extends RouteGroup {
        public function __construct() {
            parent::__construct("bills", "\AuditCongress\Bills");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBioguideId", ["bioguideId"]);
            $this->addCustomRoute(new BillsByFilter());
        }
    }
    class BillsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\Bills", "getByFilter", []);
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