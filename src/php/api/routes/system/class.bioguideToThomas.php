<?php

namespace API {
    class BioguideToThomas extends RouteGroup {
        public function __construct() {
            parent::__construct("bioguideToThomas", "\AuditCongress\Members");
            $this->addRoute("getBioguideToThomasIdMapping");
        }
    }
}

?>