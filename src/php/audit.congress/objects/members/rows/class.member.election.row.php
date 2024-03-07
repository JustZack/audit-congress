<?php

namespace AuditCongress {

    use \MySqlConnector\SqlRow;

    class MemberElectionRow extends SqlRow {
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