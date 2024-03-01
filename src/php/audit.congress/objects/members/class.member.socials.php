<?php 

namespace AuditCongress {

    class MemberSocials extends \MySqlConnector\SqlRow {
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
                return ["bioguideId"];
            }
        
            public function getValues() {
                return [$this->bioguideId];
            }
    }
}

?>