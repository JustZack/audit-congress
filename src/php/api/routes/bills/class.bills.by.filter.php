<?php

namespace API {
    class BillsByFilter extends BillsRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getIfSet("congress", "int");
            $type = Parameters::getIfSet("type");
            $number = Parameters::getIfSet("number", "int");
            $title = Parameters::getIfSet("title");
            $sort = Parameters::getIfSet("sort");

            if ($sort == null) return \AuditCongress\Bills::getByFilter($congress, $type, $number, $title);
            else               return \AuditCongress\Bills::getByFilter($congress, $type, $number, $title, $sort);
            
        }
    }
}

?>