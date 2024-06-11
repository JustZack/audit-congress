<?php

namespace API {
    class Subjects extends RouteGroup {
        public function __construct() {
            parent::__construct("subjects", "\AuditCongress\BillSubjects");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addCustomRoute(new SubjectsByFilter());
        }
    }
    class SubjectsByFilter extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\BillSubjects", "getByFilter", []);
        }
        //Note: No required parameters        
        public function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $subject = Parameters::get("subject");

            return $this->getCallableFunction()($congress, $type, $number, $subject);
        }
    }
}

?>