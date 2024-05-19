<?php 

namespace AuditCongress {

    class MemberOfficesQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery;

        public function __construct() {
            parent::__construct("MemberOffices");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["officeId"], false);
        }

        public static function getByOfficeId($officeId) {
            $offices = new MemberOfficesQuery();
            $offices->setSearchColumns(["officeId"]);
            $offices->setSearchValues([$officeId]);
            return $offices->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>