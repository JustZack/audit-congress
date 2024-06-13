<?php

namespace API {
    class Reports extends RouteGroup {
        public function __construct() {
            parent::__construct("reports", "\AuditCongress\BillCommitteeReports");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new ReportsByFilter());
        }
    }
    class ReportsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillCommitteeReports", "getByFilter", []);
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