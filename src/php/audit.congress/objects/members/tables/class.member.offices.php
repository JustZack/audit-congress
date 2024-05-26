<?php 

namespace AuditCongress {

    class MemberOffices extends MemberTable {
        use GetByBioguideId, GetById;
        private function __construct() {
            parent::__construct("MemberOffices", "MemberOfficesQuery", "MemberOfficesRow");
        }

        private static $memberOfficesTable = null;
        public static function getInstance() {
            if (self::$memberOfficesTable == null) 
                self::$memberOfficesTable = new MemberOffices();
            return self::$memberOfficesTable;
        }
    }
}

?>