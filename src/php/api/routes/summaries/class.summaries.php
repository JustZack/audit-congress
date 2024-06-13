<?php

namespace API {
    class Summaries extends RouteGroup {
        public function __construct() {
            parent::__construct("summaries", "\AuditCongress\BillSummaries");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int", "text" => "string"]);
        }
    }
}

?>