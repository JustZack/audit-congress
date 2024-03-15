<?php 

namespace AuditCongress {
    
    use \UnitedStatesLegislators\Socials;

    class MemberSocials extends MemberTable {
        
        private function __construct() {
            parent::__construct("MemberSocials");
        }

        public function updateCache() {
            //Clear out all data associated with socials
            $this->clearRows();

            $socials = new Socials();

            foreach ($socials->legislatorSocialMedia as $personWithSocials) {
                $bioguideId = $personWithSocials->id->bioguide;
                
                $social = self::apiSocialToRow($personWithSocials, $bioguideId);
                $row = new MemberSocialsRow($social);
                $this->queueInsert($row);
            }
            $this->commitInsert();
            $this->cacheIsValid = true;
        }

        private static function apiSocialToRow($socialPerson, $bioguideId) {
            $rowArray = $socialPerson->getSocials()->toArray();
            $rowArray["bioguideId"] = $bioguideId;
            $rowArray = self::setUpdateTimes($rowArray);
            return $rowArray;
        }

        private static $memberSocialsTable = null;
        public static function getInstance() {
            if (self::$memberSocialsTable == null) 
                self::$memberSocialsTable = new MemberSocials();
            return self::$memberSocialsTable;
        }

        protected static function parseResult($resultRows) {
            return MemberSocialsRow::rowsToObjects($resultRows);
        }

        public static function getByBioguideId($bioguideId) {
            self::enforceCache();
            $socials = MemberSocialsQuery::getByBioguideId($bioguideId);
            return self::returnFirst(self::parseResult($socials));
        }
    }
}

?>