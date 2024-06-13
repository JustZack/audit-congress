<?php

namespace AuditCongress {

    class MemberElectionRow extends AuditCongressRow {
        public
            $fecId,
            $bioguideId;

        public function getColumns() { return self::getTableColumns("MemberElections"); }

        public function getValues() {
            return [$this->fecId,$this->bioguideId];
        }
    }
}

?>