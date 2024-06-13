<?php

namespace API {
    class Session extends RouteGroup {
        public function __construct() {
            parent::__construct("session", "\AuditCongress\Sessions");
            $this->addRoute("getByCongress", ["congress"]);
            $this->addRoute("getByCongressAndNumber", ["congress", "number"]);
            $this->addRoute("getByCongressAndChamber", ["congress", "chamber"]);
            $this->addRoute("getByCongressNumberAndChamber", ["congress", "number", "chamber"]);
            $this->addRoute("getByDate", ["date"]);
            $this->addRoute("getCurrent", ["current"]);
            $this->addRoute("getAll");
        }
    }
}

?>