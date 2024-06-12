<?php

namespace API {
    class Summaries extends RouteGroup {
        public function __construct() {
            parent::__construct("summaries", "\AuditCongress\BillSummaries");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new SummariesByFilter);
        }
    }
    class SummariesByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillSummaries", "getByFilter", []);
        }
        //Note: No required parameters        
        public function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $text = Parameters::get("text");

            return $this->getCallableFunction()($congress, $type, $number, $text);
        }
    }
}

?>