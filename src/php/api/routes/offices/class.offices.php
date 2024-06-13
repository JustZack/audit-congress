<?php

namespace API {
    class Offices extends RouteGroup {
        public function __construct() {
            parent::__construct("offices", "\AuditCongress\MemberOffices");
            $this->addRoute("getById", ["id"]);
            $this->addRoute("getByBioguideId", ["bioguideId"]);
        }
    }
}

?>