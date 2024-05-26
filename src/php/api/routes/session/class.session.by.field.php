<?php

namespace API {
    class SessionsByCongress extends SessionRoute {

        public static function parameters() { return ["congress"]; }
        
        public static function fetchResult() {
            $congress = Parameters::get("congress");
            return \AuditCongress\Sessions::getByCongress($congress);
        }
    }

    class SessionsByCongressAndNumber extends SessionRoute {

        public static function parameters() { return ["congress", "number"]; }
        
        public static function fetchResult() {
            $congress = Parameters::get("congress");
            $number = Parameters::get("number");
            return \AuditCongress\Sessions::getByCongressAndNumber($congress, $number);
        }
    }

    class SessionsByCongressAndChamber extends SessionRoute {

        public static function parameters() { return ["congress", "chamber"]; }
        
        public static function fetchResult() {
            $congress = Parameters::get("congress");
            $chamber = Parameters::get("chamber");
            return \AuditCongress\Sessions::getByCongressAndChamber($congress, $chamber);
        }
    }

    class SessionsByCongressNumberAndChamber extends SessionRoute {

        public static function parameters() { return ["congress", "number", "chamber"]; }
        
        public static function fetchResult() {
            $congress = Parameters::get("congress");
            $number = Parameters::get("number");
            $chamber = Parameters::get("chamber");
            return \AuditCongress\Sessions::getByCongressNumberAndChamber($congress, $number, $chamber);
        }
    }

    class SessionByDate extends SessionRoute {

        public static function parameters() { return ["date"]; }
        
        public static function fetchResult() {
            $date = Parameters::get("date");
            return \AuditCongress\Sessions::getByDate($date);
        }
    }

    class CurrentSessions extends SessionRoute {

        public static function parameters() { return ["current"]; }
        
        public static function fetchResult() {
            return \AuditCongress\Sessions::getCurrent();
        }
    }

    class AllSessions extends SessionRoute {

        public static function fetchResult() {
            return \AuditCongress\Sessions::getAll();
        }
    }
}

?>