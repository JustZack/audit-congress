<?php 

namespace AuditCongress {

    class MemberElections extends MemberTable {
        use \Util\GetInstance, GetByBioguideId;

        private function __construct() {
            parent::__construct("MemberElections", "MemberElectionsQuery", "MemberElectionRow");
        }

        public static function getByFecId($fecId) {
            self::enforceCache();
            $elections = MemberElectionsQuery::getByFecId($fecId);
            return self::returnFirst(self::parseResult($elections));
        }
    }
}

?>