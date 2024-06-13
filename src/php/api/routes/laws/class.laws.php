<?php

namespace API {
    class Laws extends RouteGroup {
        public function __construct() {
            parent::__construct("laws", "\AuditCongress\BillLaws");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int"]);
        }
    }
}

?>