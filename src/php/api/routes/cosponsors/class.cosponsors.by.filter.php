<?php

namespace API {
    class CosponsorsByFilter extends CosponsorsRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getIfSet("congress", "int");
            $type = Parameters::getIfSet("type");
            $number = Parameters::getIfSet("number", "int");
            $bioguideId = Parameters::getIfSet("bioguideId");
            $sort = Parameters::getIfSet("sort");

            if ($sort == null) return \AuditCongress\BillCosponsors::getByFilter($congress, $type, $number, $bioguideId);
            else               return \AuditCongress\BillCosponsors::getByFilter($congress, $type, $number, $bioguideId, $sort);
            
        }
    }
}

?>