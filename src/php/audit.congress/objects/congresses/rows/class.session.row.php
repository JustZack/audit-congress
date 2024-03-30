<?php

namespace AuditCongress {

    class SessionRow extends \MySqlConnector\SqlRow {
        public
            $congress,
            $number,
            $chamber,
            $type,
            $startDate,
            $endDate;

        public function getColumns() {
            return ["congress","number","chamber",
                    "type","startDate","endDate"];
        }
    
        public function getValues() {
            return [$this->congress,$this->number,$this->chamber,
                    $this->type,$this->startDate, $this->endDate];
        }
    }
}

?>