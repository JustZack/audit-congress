<?php

namespace AuditCongress {

    class MemberRow extends AuditCongressRow {
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
            $isCurrent;
    
        public function getColumns() { return self::getTableColumns("Members"); }

        public function getValues() {
            return [$this->bioguideId,$this->thomasId,$this->lisId,$this->govTrackId,
            $this->openSecretsId,$this->voteSmartId,$this->cspanId,$this->mapLightId,
            $this->icpsrId,$this->wikidataId,$this->googleEntityId,
            $this->official_full,$this->first,$this->last,$this->gender,
            $this->birthday,$this->imageUrl,$this->imageAttribution,$this->isCurrent];
        }
    }
}

?>