<?php

namespace AuditCongress {

    class MemberElectionRow extends \MySqlConnector\SqlRow {
        public
            $fecId,
            $bioguideId,
            $lastUpdate,
            $nextUpdate;

        public function getColumns() {
            return ["fecId","bioguideId",
            "lastUpdate","nextUpdate"];
        }
    
        public function getValues() {
            return [$this->fecId,$this->bioguideId,
            $this->lastUpdate,$this->nextUpdate];
        }
    }
}

?>