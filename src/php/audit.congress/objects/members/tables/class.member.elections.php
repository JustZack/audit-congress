<?php 

namespace AuditCongress {

    class MemberElections extends MemberTable {
        use GetByBioguideId;

        private function __construct() {
            parent::__construct("MemberElections", "\AuditCongress\MemberElectionsQuery");
        }

        private static $memberTermsTable = null;
        public static function getInstance() {
            if (self::$memberTermsTable == null) 
                self::$memberTermsTable = new MemberElections();
            return self::$memberTermsTable;
        }

        protected static function parseResult($resultRows) {
            return MemberElectionRow::rowsToObjects($resultRows);
        }

        public static function getByFecId($fecId) {
            self::enforceCache();
            $elections = MemberElectionsQuery::getByFecId($fecId);
            return self::returnFirst(self::parseResult($elections));
        }
    }
}

?>