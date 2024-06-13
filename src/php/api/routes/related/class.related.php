<?php

namespace API {
    class Related extends RouteGroup {
        public function __construct() {
            parent::__construct("related", "\AuditCongress\BillRelatedBills");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int"]);
        }
    }
}

?>