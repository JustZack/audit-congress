<?php 

namespace AuditCongress {

    class MemberOffices extends MemberTable {

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

        public static function getByBioguideId($bioguideId) {
            $offices = MemberOfficesQuery::getByBioguideId($bioguideId);
            return self::parseResult($offices);
        }

        public static function getByOfficeId($officeId) {
            self::enforceCache();
            $offices = MemberOfficesQuery::getByOfficeId($officeId);
            return self::parseResult($offices);
        }
    }
}

?>