<?php

namespace API {
    class MemberByAnyName extends Route {

        public static function parameters() { return ["name"]; }

        public static function fetchResult() {
            $args = self::fetchParameters();
            $current = Parameters::getIfSet("current", "bool");
            return \AuditCongress\Members::getByAnyName($args["name"], $current);
        }
    }
}

?>