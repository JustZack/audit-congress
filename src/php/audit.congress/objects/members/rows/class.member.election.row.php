<?php

namespace AuditCongress {

    class MemberElectionRow extends \MySqlConnector\SqlRow {
        public
            $fecId,
            $bioguideId;

        public function getColumns() {
            return ["fecId","bioguideId"];
        }
    
        public function getValues() {
            return [$this->fecId,$this->bioguideId];
        }
    }
}

?>