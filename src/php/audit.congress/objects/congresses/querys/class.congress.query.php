<?php 

namespace AuditCongress {

    use MySqlConnector\Comparison;

    class CongressQuery extends AuditCongressQuery {
        use TruncateRowsQuery, InsertQueueingQuery;

        public function __construct() {
            parent::__construct("Congresses");
        }

        public function applyDefaultOrder() {
            $this->setOrderBy(["number"], false);
        }

        public static function getByNumber($congressNumber) {
            $congresses = new CongressQuery();
            $congresses->addSearchValue("number", "=", $congressNumber); 
            $congresses->addSearch("number", Comparison::EQUALS, $congressNumber);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByYear($year) {
            $congresses = new CongressQuery();
            $congresses->addSearchValue("startYear", "<=", $year);
            $congresses->addSearchValue("endYear", ">=", $year); 
            $congresses->addSearch("startYear", Comparison::LESS_THAN_EQUALS, $year);
            $congresses->addSearch("endYear", Comparison::GREATER_THAN_EQUALS, $year);
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