<?php 

namespace AuditCongress {

    class Bill {

        public function __construct($congress, $type, $number) {

        }
    }

    class SQLBill extends \MySqlConnector\SqlObject {

        //Get and setup an object meant to select a row in SQL
        //Expects [$congress, $type, $number]
        public static function getSelectObject($values) {
            list(0=>$congress, 1=>$type, 2=>$number) = $values;
            $selectObj = new SQLBill();
            $selectObj->setColumns(["congress", "type", "number"]);
            $selectObj->setValues([$congress, $type, $number]);
            $selectObj->setSelectColumns(["congress", "type", "number", "title", ""]);
            return $selectObj;
        }
        //Get and setup an object meant to manipulate a row in SQL
        //Expects [$congress, $type, $number, $title, $sponsorBioguideID]
        public static function getManipulateObject($values) {
            list("congres"=>$congress, "type"=>$type, "number"=>$number, "title"=>$title, "sponsor"=>$sponsorBioguideID) = $values;
            $manipObj = new SQLBill();
            $manipObj->setColumns(["congress", "type", "number", "title", "sponsor"]);
            $manipObj->setValues([$congress, $type, $number, $title, $sponsorBioguideID]);
            return $manipObj;
        }
        //Generate a condition that matches to this object
        public function whereCondition() { return $this->buildORConditon(); }
    }
}

?>