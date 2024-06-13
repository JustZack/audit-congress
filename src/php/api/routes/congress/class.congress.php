<?php

namespace API {
    class Congress extends RouteGroup {
        public function __construct() {
            parent::__construct("congress", "\AuditCongress\Congresses");
            $this->addRoute("getByNumber", ["number" => "int"]);
            $this->addRoute("getByYear", ["year" => "string"]);
            $this->addRoute("getCurrent", ["current" => "string"]);
            $this->addRoute("getAll");
        }
    }
}

?>