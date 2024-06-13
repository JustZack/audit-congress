<?php

namespace API {
    class Summaries extends RouteGroup {
        public function __construct() {
            parent::__construct("summaries", "\AuditCongress\BillSummaries");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int", "text" => "string"]);
        }
    }
}

?>