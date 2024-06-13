<?php

namespace API {
    class Elections extends RouteGroup {
        public function __construct() {
            parent::__construct("elections", "\AuditCongress\MemberElections");
            $this->addRoute("getByBioguideId", ["id"]);
            $this->addRoute("getByFecId", ["fecid"]);
        }
    }
}

?>