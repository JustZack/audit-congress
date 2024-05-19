<?php 

namespace AuditCongress {
    
    class MemberSocialsQuery extends AuditCongressQuery {
        use GetByBioguideIdQuery;

        public function __construct() {
            parent::__construct("MemberSocials");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["bioguideId"], false);
        }
    }
}

?>