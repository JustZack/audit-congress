<?php

namespace API {
    class BillsByCongressTypeNumber extends BillsRoute {

        public static function parameters() { return ["congress", "type", "number"]; }
        
        public static function fetchResult() {
            $congress = Parameters::getIfSet("congress");
            $type = Parameters::getIfSet("type");
            $number = Parameters::getIfSet("number");
            return \AuditCongress\Bills::getByCongressTypeNumber($congress, $type, $number);
        }
    }
}

?>