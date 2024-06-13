<?php

namespace API {
    class Subjects extends RouteGroup {
        public function __construct() {
            parent::__construct("subjects", "\AuditCongress\BillSubjects");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int", "subject" => "string"]);
        }
    }
}

?>