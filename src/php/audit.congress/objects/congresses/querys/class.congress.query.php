<?php 

namespace AuditCongress {

    class CongressQuery extends AuditCongressQuery {
        public function __construct() {
            parent::__construct("Congresses");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["number"], false);
        }

        public static function getByNumber($congressNumber) {
            $congresses = new CongressQuery();
            $congresses->setSearchColumns(["number"]);
            $congresses->setSearchValues([$congressNumber]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByYear($year) {
            $congresses = new CongressQuery();
            $congresses->setEqualityOperators(["<=", ">="]);
            $congresses->setSearchColumns(["startYear", "endYear"]);
            $congresses->setSearchValues([$year, $year]);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getAll() {
            $congresses = new CongressQuery();
            $congresses->setOrderBy(["number"], false);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>