<?php

namespace API {
    class BillsByCongressAndType extends BillsRoute {

        public static function parameters() { return ["congress", "type"]; }
        
        public static function fetchResult() {
            $congress = Parameters::getIfSet("congress");
            $type = Parameters::getIfSet("type");
            return \AuditCongress\Bills::getByCongressAndType($congress, $type);
        }
    }
}

?>