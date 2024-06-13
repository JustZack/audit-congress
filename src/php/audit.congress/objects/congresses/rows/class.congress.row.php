<?php

namespace AuditCongress {

    class CongressRow extends AuditCongressRow {
        public
            $number,
            $name,
            $startYear,
            $endYear;
   
        public function getColumns() { return self::getTableColumns("Congresses"); }

        public function getValues() {
            return [$this->number,$this->name,
            $this->startYear,$this->endYear];
        }
    }
}

?>