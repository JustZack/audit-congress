<?php

namespace AuditCongress {

    use \MySqlConnector\SqlRow;

    class MemberRow extends SqlRow {
        public
            $bioguideId,
            $thomasId,
            $lisId,
            $govTrackId,
            $openSecretsId,
            $voteSmartId,
            $cspanId,
            $mapLightId,
            $icpsrId,
            $wikidataId,
            $googleEntityId,

            $official_full,
            $first,
            $last,
            $gender,
            $birthday,

            $imageUrl,
            $imageAttribution,
            $isCurrent,

            $lastUpdate,
            $nextUpdate;
    
        public function getColumns() {
            return ["bioguideId","thomasId","lisId","govTrackId",
            "openSecretsId","voteSmartId","cspanId","mapLightId","icpsrId",
            "wikidataId","googleEntityId","official_full","first","last",
            "gender","birthday","imageUrl","imageAttribution","isCurrent",
            "lastUpdate","nextUpdate"];
        }

        public function getValues() {
            return [$this->bioguideId,$this->thomasId,$this->lisId,$this->govTrackId,
            $this->openSecretsId,$this->voteSmartId,$this->cspanId,$this->mapLightId,
            $this->icpsrId,$this->wikidataId,$this->googleEntityId,
            $this->official_full,$this->first,$this->last,
            $this->gender,$this->birthday,$this->imageUrl,$this->imageAttribution,
            $this->isCurrent, $this->lastUpdate,$this->nextUpdate];
        }
    }
}

?>