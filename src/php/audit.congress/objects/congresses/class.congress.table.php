<?php

namespace AuditCongress {

    abstract class CongressTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null, $rowClassName = null) {
            parent::__construct($tableName, $queryClassName, $rowClassName);
            $this->setTrackedCache("bulk-congress");
        }

        public function updateCache() { 
            $this->cacheTracker->setRunning(true, "updating");

            $sessionsInstance = Sessions::getInstance();
            $congressInstance = Congresses::getInstance();

            $congresses = new \CongressGov\Congresses();

            $congressInstance->clearRows();
            $sessionsInstance->clearRows();

            $this->insertCongresses($congresses->congresses);

            $congressInstance->commitInsert();
            $sessionsInstance->commitInsert();
            
            $this->cacheIsValid = true;

            $this->cacheTracker->setRunning(false, "done");
        }
        
        private function insertCongresses($congresses) {
            foreach ($congresses as $congress) {  
                $sessions = $congress->sessions;
                $congress = new CongressRow($congress);
                $current = $congress->number;
                $this->insertSessions($current, $sessions);
                Congresses::getInstance()->queueInsert($congress);
            }
        }

        private function insertSessions($congress, $sessions) {
            foreach ($sessions as $session) {                
                $session["congress"] = $congress;
                $session = new SessionRow($session);
                Sessions::getInstance()->queueInsert($session);
            }
        }
    }
}

?>