<?php

namespace API {
    class Session extends RouteGroup {
        public function __construct() {
            parent::__construct("session", "\AuditCongress\Sessions");
            $this->addRoute("getByCongress", ["congress" => "int"]);
            $this->addRoute("getByCongressAndNumber", ["congress" => "int", "number" => "int"]);
            $this->addRoute("getByCongressAndChamber", ["congress" => "int", "chamber" => "string"]);
            $this->addRoute("getByCongressNumberAndChamber", ["congress" => "int", "number" => "int", "chamber" => "string"]);
            $this->addRoute("getByDate", ["date" => "string"]);
            $this->addRoute("getCurrent", ["current"  => "string"]);
            $this->addRoute("getAll");
        }
    }
}

?>