<?php 

namespace AuditCongress {
    
    class MemberSocialsQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("MemberSocials");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["bioguideId"], false);
        }

        public static function getByBioguideId($bioguideId) {
            $socials = new MemberSocialsQuery();
            $socials->setSearchColumns(["bioguideId"]);
            $socials->setSearchValues([$bioguideId]);
            return $socials->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>