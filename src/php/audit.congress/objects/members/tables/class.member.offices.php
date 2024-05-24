<?php 

namespace AuditCongress {

    class MemberOffices extends MemberTable {
        use GetByBioguideId, GetById;
        private function __construct() {
            parent::__construct("MemberOffices", "\AuditCongress\MemberOfficesQuery");
        }

        private static $memberOfficesTable = null;
        public static function getInstance() {
            if (self::$memberOfficesTable == null) 
                self::$memberOfficesTable = new MemberOffices();
            return self::$memberOfficesTable;
        }

        protected static function parseResult($resultRows) {
            return MemberOfficesRow::rowsToObjects($resultRows);
        }
    }
}

?>