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
            $congresses->addSearch("number", Comparison::EQUALS, $congressNumber);
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getByYear($year) {
            $congresses = new CongressQuery();
            $congresses->addSearch("startYear", Comparison::LESS_THAN_EQUALS, $year);
            $congresses->addSearch("endYear", Comparison::GREATER_THAN_EQUALS, $year);
            $congresses->applyDefaultOrder();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }

        public static function getAll() {
            $congresses = new CongressQuery();
            $congresses->applyDefaultOrder();
            return $congresses->selectFromDB()->fetchAllAssoc();
        }
    }
}

?>