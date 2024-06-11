<?php

namespace API {
    class Member extends RouteGroup {
        public function __construct() {
            parent::__construct("members", "\API\MemberRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class MemberRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\AuditCongress\Members", $functionName, $parameters);
        }
    }
    class MemberByAnyName extends MemberRoute {
        public function __construct() {
            parent::__construct("getByAnyName", ["name"]);
        }

        public function fetchResult() {
            $name = Parameters::get("name");
            $current = Parameters::getBool("current");
            return \AuditCongress\Members::getByAnyName($name, $current);
        }
    }

    class MemberByBioguideId extends MemberRoute {
        public function __construct() {
            parent::__construct("getByBioguideId", ["id"]);
        }
    }

    class MemberByFilter extends MemberRoute {
        public function __construct() {
            parent::__construct("getByFilter", []);
        }
        //Note: No required parameters
        public function fetchResult() {
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
            

            return $this->getCallableFunction()($state, $type, $party, $gender, $current);
        }
    }
}

?>