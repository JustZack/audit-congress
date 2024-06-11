<?php

namespace API {
    class Congress extends RouteGroup {
        public function __construct() {
            parent::__construct("congress", "\AuditCongress\Congresses");
            $this->addRoute("getByNumber", ["number"]);
            $this->addRoute("getByYear", ["year"]);
            $this->addRoute("getCurrent", ["current"]);
            $this->addRoute("getAll");
        }
    }
}

?>