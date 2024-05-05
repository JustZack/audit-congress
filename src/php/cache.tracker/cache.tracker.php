<?php

namespace Cache {
    class Tracker extends TrackerConfig {
        public function getSource() { return $this->getValue("source"); }

        public function getStatus() { return $this->getValue("status"); }

        public function isUpdating() { return $this->getValue("isRunning"); }

        public function isOutOfDate() { return strtotime($this->getValue("nextUpdate")) < time(); }

        //Determine the next time this cache should update based on given config
        public function nextUpdate() {
            $nextUpdate = 0;
            //If it uses update times (24hr UTC), use these as the basis for $nextUpdate
            if ($this->usesUpdateTimes()) {
                $updateHours = $this->getUpdateTimes();
                
                $nextHour = \Util\Time::getFirstHourPastNow($updateHours);
                $offset = 0;
                //$nextHour == -1 => time() is later than all given hours
                //  So instead use the first hour for the next day
                if ($nextHour == -1) {
                    $nextHour = $updateHours[0];
                    $offset = \Util\Time::hoursToSeconds(24);
                }
                $d = new \DateTime(date("Y-m-d $nextHour:00:00"));
                $nextUpdate = $d->getTimestamp() + $offset;
            } else {
                $nextUpdate = time() + \Util\Time::hoursToSeconds($this->getUpdateInterval());
            }

            return \Util\Time::getDateTimeStr($nextUpdate);
        }
        
        //Wait up to $timeoutSeconds for the tracker row to report this cache isnt running
        public function waitForUpdate($timeoutSeconds = 16) {
            //Only run if a script is associated with the config
            $secondsSlept = 0;
            do {
                //Refresh the cache row each iteration
                $this->refresh();
                //If the tracker is running based on the db column
                if ($this->isUpdating()) {
                    //update time spent and sleep
                    $secondsSlept++;
                    sleep(1);
                } 
                //Otherwise return true to signify completion
                else return True;
            } while ($secondsSlept < $timeoutSeconds || $timeoutSeconds == 0);
            //If we made it out the sleep loop, the cache never finished running.
            return False;
        }
        /*
            Run the update script associated with this tracker.
            Only run if this tracker uses a script and isnt running already.

            Does not run setRunning(), the script within this updates that (I dont love this structure)
        */
        public function runUpdateScript($waitForComplete = true) {
            $out = array();
            if (self::usesScript() && !self::isUpdating()) {
                $runner = $this->scriptRunner();
                $path = \Util\File::getAbsolutePath($this->scriptPath());
                $dir = \Util\File::getFolderPath($path);
                $file = \Util\File::getFileName($path);

                //$post = !$waitForComplete ? " > /dev/null &" : "";
                $cmd = "cd $dir && $runner $file";
                array_push($out, $cmd);
                exec($cmd, $out);
            }
            return $out;
        }

        //Set a value about this cache in the database
        private function setCacheValue($status = null, $isRunning = null, $lastRunStart = null, $lastUpdate = null, $nextUpdate = null) {
            $function = "\Cache\TrackerQuery::updateCacheStatus";
            if (!$this->isset()) $function = "\Cache\TrackerQuery::insertCacheStatus";

            $function($this->name(), $status, $isRunning, $lastRunStart, $lastUpdate, $nextUpdate);
            $this->invalidate();
        }

        //Just set the cache status
        public function setStatus($status) { $this->setCacheValue($status); }

        /*
            Set weather or not this cache is running (updating), and update the status
            if (true): set lastRunStart to now
            if (false): set lastUpdate to now & set next update to computed time
        */
        public function setRunning($isRunning, $status = null) { 
            $nowStr = \Util\Time::getNowDateTimeStr();
            if ($isRunning) $this->setCacheValue($status, $isRunning, $nowStr); 
            else $this->setCacheValue($status, $isRunning, null, $nowStr, $this->nextUpdate());
        }
    }
}

?>