<?php

namespace AuditCongress {

    class MemberSocialsRow extends \MySqlConnector\SqlRow {
        public
            $bioguideId,
            $twitter,
            $twitterId,
            $facebook,
            $facebookId,
            $youtube,
            $youtubeId,
            $instagram,
            $instagramId;

        public function getColumns() {
            return ["bioguideId", "twitter", "twitterId", 
            "facebook", "facebookId", "youtube", "youtubeId",
            "instagram", "instagramId"];
        }
    
        public function getValues() {
            return [$this->bioguideId, $this->twitter,
            $this->twitterId,$this->facebook,$this->facebookId,
            $this->youtube,$this->youtubeId,$this->instagram,
            $this->instagramId];
        }
    }
}

?>