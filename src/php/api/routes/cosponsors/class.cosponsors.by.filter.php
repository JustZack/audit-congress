<?php

namespace API {
    class CosponsorsByFilter extends CosponsorsRoute {

        //Note: No required parameters        
        public static function fetchResult() {
            $congress = Parameters::getInt("congress");
            $type = Parameters::get("type");
            $number = Parameters::getInt("number");
            $bioguideId = Parameters::get("bioguideId");
            $sort = Parameters::getArray("sort");

            if ($sort == null) return \AuditCongress\BillCosponsors::getByFilter($congress, $type, $number, $bioguideId);
            else               return \AuditCongress\BillCosponsors::getByFilter($congress, $type, $number, $bioguideId, $sort);
            
        }
    }
}

?>