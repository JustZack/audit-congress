<?php

namespace API {
    class MemberByState extends MemberRoute {

        public static function parameters() { return ["state"]; }

        public static function fetchResult() {
            $args = self::fetchParameters();
            $current = Parameters::getIfSet("current", "bool");
            return \AuditCongress\Members::getByState($args["state"], $current);
        }
    }
}

?>