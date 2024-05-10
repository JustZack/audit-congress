<?php

namespace API {
    class MemberByAnyName extends MemberRoute {

        public static function parameters() { return ["name"]; }

        public static function fetchResult() {
            $name = Parameters::get("name");
            $current = Parameters::getBool("current");
            return \AuditCongress\Members::getByAnyName($name, $current);
        }
    }
}

?>