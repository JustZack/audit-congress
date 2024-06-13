<?php

namespace API {
    class Related extends RouteGroup {
        public function __construct() {
            parent::__construct("related", "\AuditCongress\BillRelatedBills");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new RelatedBillsByFilter());
        }
    }
    class RelatedBillsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillRelatedBills", "getByFilter", []);
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