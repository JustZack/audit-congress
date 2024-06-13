<?php

namespace API {
    class Terms extends RouteGroup {
        public function __construct() {
            parent::__construct("terms", "\AuditCongress\MemberTerms");
            $this->addRoute("getByBioguideId", ["id"]);
        }
    }
}

?>