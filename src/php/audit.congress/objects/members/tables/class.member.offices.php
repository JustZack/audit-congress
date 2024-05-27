<?php 

namespace AuditCongress {

    class MemberOffices extends MemberTable {
        use \Util\GetInstance, GetByBioguideId, GetById;
        private function __construct() {
            parent::__construct("MemberOffices", "MemberOfficesQuery", "MemberOfficesRow");
        }
    }
}

?>