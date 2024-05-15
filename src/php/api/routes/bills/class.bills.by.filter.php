<?php

namespace API {
    class BillsByFilter extends BillsRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $title = Parameters::get("title");
            $sort = Parameters::getArray("sort");

            if ($sort == null) return \AuditCongress\Bills::getByFilter($congress, $type, $number, $title);
            else               return \AuditCongress\Bills::getByFilter($congress, $type, $number, $title, $sort);
            
        }
    }
}

?>