<?php

namespace API {
    class SocialsByBioguideId extends SocialsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $args = self::fetchParameters();
            return \AuditCongress\MemberSocials::getByBioguideId($args["id"]);
        }
    }
}

?>