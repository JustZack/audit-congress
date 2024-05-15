<?php

namespace API {
    class CosponsorsById extends CosponsorsRoute {

        public static function parameters() { return ["id"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("id");
            return \AuditCongress\BillCosponsors::getById($id);
        }
    }
    class CosponsorsByBioguideId extends CosponsorsRoute {

        public static function parameters() { return ["bioguideId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("bioguideId");
            return \AuditCongress\BillCosponsors::getByBioguideId($id);
        }
    }
    class CosponsorsByBillId extends CosponsorsRoute {

        public static function parameters() { return ["billId"]; }
        
        public static function fetchResult() {
            $id = Parameters::get("billId");
            return \AuditCongress\BillCosponsors::getByBillId($id);
        }
    }
}

?>