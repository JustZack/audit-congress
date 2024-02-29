<?php 

namespace AuditCongress {

    class Member extends SqlRow {
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
    }
}

?>