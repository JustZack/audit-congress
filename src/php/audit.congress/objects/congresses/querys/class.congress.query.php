<?php 

namespace AuditCongress {

    class CongressQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("Congresses");
        }

        public static function getByNumber($congressNumber) {
            $congresses = new CongressQuery();
            $congresses->setSearchColumns(["number"]);
            $congresses->setSearchValues([$congressNumber]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getAll() {
            $congresses = new CongressQuery();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>