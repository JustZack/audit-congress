<?php

namespace API {
    class MemberByState extends MemberRoute {

        public static function parameters() { return ["state"]; }

        public static function fetchResult() {
            $args = self::fetchParameters();
            $current = Parameters::getIfSet("current", "bool");
            $gender = Parameters::getIfSet("gender");
            $type = Parameters::getIfSet("type");

            if (!self::validParameter($type, ["rep", "sen"])) self::throwException("fetchResult", "Unknown member type: $type. Use sen or rep.");
            if (!self::validParameter($gender, ["M", "F"])) self::throwException("fetchResult", "Unknown member gender: $type. Use M or F.");

            return \AuditCongress\Members::getByState($args["state"], $type, $gender, $current);
        }
    }
}

?>