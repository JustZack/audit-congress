<?php

namespace AuditCongress {

    class CongressRow extends \MySqlConnector\SqlRow {
        public
            $number,
            $name,
            $startYear,
            $endYear;

        public function getColumns() {
            return ["number","name",
            "startYear","endYear"];
        }
    
        public function getValues() {
            return [$this->number,$this->name,
            $this->startYear,$this->endYear];
        }
    }
}

?>