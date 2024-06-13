<?php

namespace API {
    class Titles extends RouteGroup {
        public function __construct() {
            parent::__construct("titles", "\AuditCongress\BillTitles");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBillId", ["billId"]);
        }
    }
}

?>