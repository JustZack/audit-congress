<?php

namespace API {
    class SocialsByBioguideId extends SocialsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\MemberSocials::getByBioguideId($id);
        }
    }
}

?>