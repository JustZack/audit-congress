<?php

namespace API {
    class Socials extends RouteGroup {
        public function __construct() {
            parent::__construct("socials", "\API\SocialsRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class SocialsRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\MemberSocials", $functionName, $parameters);
        }
    }
    class SocialsByBioguideId extends SocialsRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["id"]);
        }
    }
}

?>