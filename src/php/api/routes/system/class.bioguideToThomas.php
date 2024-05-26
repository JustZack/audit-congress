<?php

namespace API {
    class BioguideToThomas extends RouteGroup {
        private function __construct() {
            parent::__construct("bioguideToThomas", "\API\BioguideToThomasRoute");
        }

        private static $bioguideToThomasInstance = null;
        public static function getInstance() {
            if (self::$bioguideToThomasInstance == null) self::$bioguideToThomasInstance = new \API\BioguideToThomas();
            return self::$bioguideToThomasInstance;
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class BioguideToThomasRoute extends Route { }

    class BioguideToThomasSingle extends BioguideToThomasRoute {
        public static function fetchResult() {
            return \AuditCongress\Members::getBioguideToThomasIdMapping();
            
        }
    }
}

?>