<?php 

namespace AuditCongress {

    class MemberSocials extends MemberTable {
        
        private function __construct() {
            parent::__construct("MemberSocials", "\AuditCongress\MemberSocialsQuery");
        }

        private static $memberSocialsTable = null;
        public static function getInstance() {
            if (self::$memberSocialsTable == null) 
                self::$memberSocialsTable = new MemberSocials();
            return self::$memberSocialsTable;
        }

        protected static function parseResult($resultRows) {
            return MemberSocialsRow::rowsToObjects($resultRows);
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $socials = MemberSocialsQuery::getByBioguideId($bioguideId);
            return self::returnFirst(self::parseResult($socials));
        }
    }
}

?>