<?php

namespace API {
    class MemberByFilter extends MemberRoute {

        //Note: No required parameters
        public static function fetchResult() {
            $state = Parameters::get("state");
            $type = Parameters::get("type");
            $party = Parameters::get("party");
            $gender = Parameters::get("gender");
            $current = Parameters::getBool("current");

            if (\Util\General::allNull($state, $type, $party, $gender, $current)) 
                self::throwException("fetchResult", "Must provde atleast one of [state, type, party, gender, current]");
            if (!self::validParameter($type, ["rep", "sen"])) 
                self::throwException("fetchResult", "Unknown member type: $type. Use sen or rep.");
            if (!self::validParameter($gender, ["M", "F"])) 
                self::throwException("fetchResult", "Unknown member gender: $type. Use M or F.");
            

            return \AuditCongress\Members::getByFilter($state, $type, $party, $gender, $current);
        }
    }
}

?>