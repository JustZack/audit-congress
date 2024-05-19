<?php 

namespace AuditCongress {

    class MemberOfficesQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("MemberOffices");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["officeId"], false);
        }

        public static function getByBioguideId($bioguideId) {
            $offices = new MemberOfficesQuery();
            $offices->setSearchColumns(["bioguideId"]);
            $offices->setSearchValues([$bioguideId]);
            return $offices->selectFromDB()->fetchAllAssoc();
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