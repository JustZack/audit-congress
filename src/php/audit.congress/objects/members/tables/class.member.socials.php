<?php 

namespace AuditCongress {

    class MemberSocials extends MemberTable {
        use \Util\GetInstance, GetByBioguideId;

        private function __construct() {
            parent::__construct("MemberSocials", "MemberSocialsQuery", "MemberSocialsRow");
        }
    }
}

?>