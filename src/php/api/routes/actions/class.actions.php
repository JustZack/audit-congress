<?php

namespace API {
    class Actions extends RouteGroup {
        public function __construct() {
            parent::__construct("actions", "\AuditCongress\BillActions");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
        }
    }
}

?>