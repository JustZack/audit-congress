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
            $instagramId,

            $lastUpdate,
            $nextUpdate;

            public function getColumns() {
                return ["bioguideId", "twitter", "twitterId", 
                "facebook", "facebookId", "youtube", "youtubeId",
                "instagram", "instagramId", "lastUpdate", "nextUpdate"];
            }
        
            public function getValues() {
                return [$this->bioguideId, $this->twitter,
                $this->twitterId,$this->facebook,$this->facebookId,
                $this->youtube,$this->youtubeId,$this->instagram,
                $this->instagramId, $this->lastUpdate, $this->nextUpdate];
            }
    }

    class MemberSocialsQuery extends \MySqlConnector\SqlObject {
        public function __construct() {
            parent::__construct("MemberSocials");
        }

        public static function getByBioguideId($bioguideId) {
            $socials = new MemberSocialsQuery();
            $socials->setSelectColumns(["*"]);
            $socials->setColumns(["bioguideId"]);
            $socials->setValues([$bioguideId]);
            return $socials->selectFromDB();
        }
    }

    class MemberSocials extends MemberTables {
        
        private function __construct() {
            parent::__construct("MemberSocials");
        }

        protected function updateCache() {
            //Clear out all data associated with socials
            $this->clearRows();

            $socials = new \UnitedStatesLegislators\Socials();
            $socials->fetchFromApi();

            foreach ($socials->legislatorSocialMedia as $personWithSocials) {
                $bioguideId = $personWithSocials->id->bioguide;
                $social = $personWithSocials->getSocials()->toArray();
                $social = self::apiSocialToRow($social, $bioguideId);
                $social = self::setUpdateTimes($social);
                $row = new MemberSocialsRow($social);
                $this->insertRow($row);
            }
            $this->cacheIsValid = true;
        }

        private static function apiSocialToRow($rowArray, $bioguideId) {
            $rowArray["bioguideId"] = $bioguideId;
            return $rowArray;
        }

        private static $memberSocialsTable = null;
        public static function getInstance() {
            if (self::$memberSocialsTable == null) 
                self::$memberSocialsTable = new MemberSocials();
            return self::$memberSocialsTable;
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            return MemberSocialsQuery::getByBioguideId($bioguideId);
        }
    }
}

?>