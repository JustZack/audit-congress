<?php

namespace API {
    class Reports extends RouteGroup {
        public function __construct() {
            parent::__construct("reports", "\AuditCongress\BillCommitteeReports");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int"]);
        }
    }
}

?>