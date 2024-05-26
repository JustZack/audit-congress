<?php 

namespace AuditCongress {

    class MemberSocials extends MemberTable {
        use GetByBioguideId;

        private function __construct() {
            parent::__construct("MemberSocials", "MemberSocialsQuery", "MemberSocialsRow");
        }

        private static $memberSocialsTable = null;
        public static function getInstance() {
            if (self::$memberSocialsTable == null) 
                self::$memberSocialsTable = new MemberSocials();
            return self::$memberSocialsTable;
        }
    }
}

?>