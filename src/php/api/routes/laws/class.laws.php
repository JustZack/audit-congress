<?php

namespace API {
    class Laws extends RouteGroup {
        public function __construct() {
            parent::__construct("laws", "\AuditCongress\BillLaws");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new LawsByFilter());
        }
    }
    class LawsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillLaws", "getByFilter", []);
        }
        //Note: No required parameters        
        public function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");

            return $this->getCallableFunction()($congress, $type, $number);
        }
    }
}

?>