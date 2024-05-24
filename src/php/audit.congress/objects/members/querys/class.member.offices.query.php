<?php 

namespace AuditCongress {

    class MemberOfficesQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery, GetByIdQuery;

        public function __construct() {
            parent::__construct("MemberOffices");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["id"], false);
        }
    }
}

?>