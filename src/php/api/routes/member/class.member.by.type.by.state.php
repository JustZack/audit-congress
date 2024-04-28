<?php

namespace API {
    class MemberByTypeByState extends MemberRoute {

        public static function parameters() { return ["state", "type"]; }

        public static function fetchResult() {
            $args = self::fetchParameters();
            $current = Parameters::getIfSet("current", "bool");
            $type = Parameters::getIfSet("type");
            
            $function = null;
            switch ($type) {
                case "rep": $function = "getRepresentatives"; break;
                case "sen": $function = "getSenators"; break;
            }
            if ($function == null) self::throwException("fetchResult", "Unknown member type: $type. Use sen or rep.");
            else return ("\AuditCongress\Members::$function")($args["state"], $current);
        }
    }
}

?>