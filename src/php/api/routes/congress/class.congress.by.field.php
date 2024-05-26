<?php

namespace API {
    class CongressByNumber extends CongressRoute {

        public static function parameters() { return ["number"]; }
        
        public static function fetchResult() {
            $number = Parameters::get("number");
            return \AuditCongress\Congresses::getByNumber($number);
        }
    }

    class CongressByYear extends CongressRoute {

        public static function parameters() { return ["year"]; }
        
        public static function fetchResult() {
            $year = Parameters::get("year");
            return \AuditCongress\Congresses::getByYear($year);
        }
    }

    class CurrentCongress extends CongressRoute {

        public static function parameters() { return ["current"]; }
        
        public static function fetchResult() {
            return \AuditCongress\Congresses::getCurrent();
        }
    }

    class AllCongresses extends CongressRoute {

        public static function fetchResult() {
            return \AuditCongress\Congresses::getAll();
        }
    }
}

?>