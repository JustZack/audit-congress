<?php 

namespace AuditCongress {

    use \MySqlConnector\SqlObject;

    class MemberElectionsQuery extends SqlObject {
        public function __construct() {
            parent::__construct("MemberElections");
        }

        public static function getByBioguideId($bioguideId) {
            $terms = new MemberElectionsQuery();
            $terms->setSearchColumns(["bioguideId"]);
            $terms->setSearchValues([$bioguideId]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }

        public static function getByFecId($fecId) {
            $terms = new MemberElectionsQuery();
            $terms->setSearchColumns(["fecId"]);
            $terms->setSearchValues([$fecId]);
            return $terms->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>