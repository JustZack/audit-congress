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
                $this->youtube,$this->youtubeId,$this->instagram,$this->instagramId];
            }
    }

    class MemberSocials extends \MySqlConnector\SqlObject {
        private static $tableName = "MemberSocials";
        private static ?\MySqlConnector\Table $staticTable = null;
        public function __construct() {
            parent::__construct(self::$tableName);
            if (self::$staticTable == null) self::$staticTable = $this->table;
            self::enforceCache();
        }

        private function enforceCache() {
            if (!self::cacheIsValid()) self::updateCache();
        }

        public static function getTable() { return self::$staticTable; }

        private static $cacheIsValid = null;
        private static function cacheIsValid() {
            if (self::$cacheIsValid != null) return self::$cacheIsValid;

            $table = self::getTable();
            $topRow = $table->select(["lastUpdate", "nextUpdate"], null, null, 1)->fetchAssoc();
            if ($topRow != null) {
                $next = (int)$topRow["nextUpdate"]-100000000;
                return !($next == false || $next < time());
            } else return false;
        }

        private static function updateCache() {
            //Clear out all data associated with members
            self::getTable()->truncate();

            $socials = new \UnitedStatesLegislators\Socials();
            $socials->fetchFromApi();

            $table = self::getTable();
            foreach ($socials->legislatorSocialMedia as $personWithSocials) {
                $bioId = $personWithSocials->id->bioguide;
                $social = $personWithSocials->getSocials()->toArray();
                $social["bioguideId"] = $bioId;
                $social["lastUpdate"] = time();
                $social["nextUpdate"] = time()+(60*60*24*7);
                $row = new MemberSocialsRow($social);
                $table->insert($row->getColumns(), $row->getValues());
            }
            self::$cacheIsValid = true;
        }

        public static function getByBioguideId($bioguideId) {
            $offices = new MemberSocials();
            $offices->setSelectColumns(["*"]);
            $offices->setColumns(["bioguideId"]);
            $offices->setValues([$bioguideId]);
            return $offices->selectFromDB();
        }
    }
}

?>