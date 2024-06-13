<?php

namespace AuditCongress {

    class SessionRow extends AuditCongressRow {
        public
            $congress,
            $number,
            $chamber,
            $type,
            $startDate,
            $endDate;

        public function getColumns() { return self::getTableColumns("Sessions"); }

    
        public function getValues() {
            return [$this->congress,$this->number,$this->chamber,
                    $this->type,$this->startDate, $this->endDate];
        }
    }
}

?>