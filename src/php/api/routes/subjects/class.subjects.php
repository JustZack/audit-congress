<?php

namespace API {
    class Subjects extends RouteGroup {
        public function __construct() {
            parent::__construct("subjects", "\API\SubjectsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SubjectsRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\BillSubjects", $functionName, $parameters);
        }
    }
    class SubjectsById extends SubjectsRoute {
        public function __construct() {
            parent::__construct("getById", ["id"]);
        }
    }
    class SubjectsByBillId extends SubjectsRoute {
        public function __construct() {
            parent::__construct("getByBillId", ["billId"]);
        }
    }
    class SubjectsByFilter extends SubjectsRoute {
        public function __construct() {
            parent::__construct("getByFilter", []);
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