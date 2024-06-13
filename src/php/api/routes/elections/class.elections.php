<?php

namespace API {
    class Elections extends RouteGroup {
        public function __construct() {
            parent::__construct("elections", "\AuditCongress\MemberElections");
            $this->addRoute("getByBioguideId", ["id" => "string"]);
            $this->addRoute("getByFecId", ["fecid" => "string"]);
        }
    }
}

?>