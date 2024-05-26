<?php 

namespace AuditCongress {

    class MemberElectionsQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery;

        public function __construct() {
            parent::__construct("MemberElections");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["fecId", "bioguideId"], false);
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