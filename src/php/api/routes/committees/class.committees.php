<?php

namespace API {
    class Committees extends RouteGroup {
        public function __construct() {
            parent::__construct("committees", "\AuditCongress\BillCommittees");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
        }
    }
}

?>