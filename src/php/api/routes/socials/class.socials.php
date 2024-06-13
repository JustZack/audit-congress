<?php

namespace API {
    class Socials extends RouteGroup {
        public function __construct() {
            parent::__construct("socials", "\AuditCongress\MemberSocials");
            $this->addRoute("getByBioguideId", ["id" => "string"]);
        }
    }
}

?>