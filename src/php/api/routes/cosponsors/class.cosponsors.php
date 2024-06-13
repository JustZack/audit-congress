<?php

namespace API {
    class Cosponsors extends RouteGroup {
        public function __construct() {
            parent::__construct("cosponsors", "\AuditCongress\BillCosponsors");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBioguideId", ["bioguideId" => "string"]);
            $this->addRoute("getByBillId", ["billId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int", "bioguideId" => "string", "sort" => "array"]);
        }
    }
}

?>