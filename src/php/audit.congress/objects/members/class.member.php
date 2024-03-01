<?php 

namespace AuditCongress {

    class Member extends \MySqlConnector\SqlRow {
        public
            $bioguideId,
            $thomasId,
            $listId,
            $govTrackId,
            $openSecretsId,
            $voteSmartId,
            $cspanId,
            $mapLightId,
            $icpsrId,
            $wikidata,
            $googleEntityId,

            $OfficialFullName,
            $FirstName,
            $MiddleName,
            $LastName,
            $Gender,
            $BirthYear,
            $DeathYear,
            $imageUrl,
            $imageAttribution,
            $lastUpdate,
            $nextUpdate;
    
        public function getColumns() {
            return ["bioguideId"];
        }

        public function getValues() {
            return [$this->bioguideId];
        }
    }
}

?>