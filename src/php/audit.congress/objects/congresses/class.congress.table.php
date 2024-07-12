<?php

namespace AuditCongress {

    abstract class CongressTable extends CacheTrackedTable {

        public function __construct($tableName, $queryClassName = null, $rowClassName = null) {
            parent::__construct($tableName, $queryClassName, $rowClassName);
            $this->setTrackedCache("bulk-congress");
        }

        public function updateCache() { 
            $this->cacheTracker->setRunning(true, "updating");

            $congresses = new \CongressGov\Congresses();

            Congresses::truncateRows();
            Sessions::truncateRows();

            $this->insertCongresses($congresses->congresses);

            Congresses::commitInsert();
            Sessions::commitInsert();
            
            $this->cacheIsValid = true;

            $this->cacheTracker->setRunning(false, "done");
        }
        
        private function insertCongresses($congresses) {
            foreach ($congresses as $congress) {  
                $sessions = $congress->sessions;
                $congress = new CongressRow($congress);
                $current = $congress->number;
                $this->insertSessions($current, $sessions);
                Congresses::queueInsert($congress);
            }
        }

        private function insertSessions($congress, $sessions) {
            foreach ($sessions as $session) {                
                $session["congress"] = $congress;
                $session = new SessionRow($session);
                //Sentinel value helps find the current sessions
                if ($session->endDate == null) $session->endDate = "0000-00-00";
                Sessions::queueInsert($session);
            }
        }
    }
}

?>