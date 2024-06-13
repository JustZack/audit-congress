<?php

namespace API {
    class Texts extends RouteGroup {
        public function __construct() {
            parent::__construct("texts", "\AuditCongress\BillTextVersions");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
        }
    }
}

?>