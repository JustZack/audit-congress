<?php

namespace Cache {
    class TrackerConfig {
        private 
            $name,
            $status,
            $updateIntervalInHours,
            $updateTimesIn24HrUTC,
            $scriptPath,
            $scriptRunner,
            $config = null;
            
        
        private function getSettingIfSet($name) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        }

        private function initUpdateConfig() {
            $updateTimes =$this->getSettingIfSet("updateTimesIn24HrUTC");
            $updateInterval = $this->getSettingIfSet("updateIntervalInHours");
            if (isset($updateTimes) && count($updateTimes) > 0) {
                $this->updateIntervalInHours = false;
                $this->updateTimesIn24HrUTC = $updateTimes;
            } else if (isset($updateInterval)) {
                $this->updateIntervalInHours = $updateInterval;
                $this->updateTimesIn24HrUTC = false;
            }
        }
        
        public function __construct($name, $settings) {
            $this->config = $settings;

            $this->name = $name;
            $this->status = $this->getSettingIfSet("status");
            $this->scriptPath = $this->getSettingIfSet("scriptPath");
            $this->scriptRunner = $this->getSettingIfSet("scriptRunner");
            $this->updateIntervalInHours = false;
            $this->updateTimesIn24HrUTC = false;
            $this->initUpdateConfig();
        }



        public function config() { return $this->config; }
        public function name() { return $this->name; }
        public function defaultStatus() { return $this->status; }

        public function getUpdateInterval() { return $this->updateIntervalInHours; }
        public function setUpdateInterval($hourInterval) {
            $this->updateIntervalInHours = $hourInterval;
        }
        public function usesUpdateInterval() {
            return $this->updateIntervalInHours != false;
        }

        public function getUpdateTimes() { return $this->updateTimesIn24HrUTC; }
        public function setUpdateTimes($UTChoursArray) {
            $this->updateTimesIn24HrUTC = $UTChoursArray;
        }
        public function usesUpdateTimes() {
            return $this->updateTimesIn24HrUTC != false && count($this->updateTimesIn24HrUTC) > 0;
        }

        public function usesScript() {
            return isset($this->scriptRunner) && isset($this->scriptPath);
        }
        public function scriptPath() { return $this->scriptPath; }
        public function scriptRunner() { return $this->scriptRunner; }
        //Set the script used by this cache
        public function setScript($runner, $path) {
            $this->scriptRunner = $runner;
            $this->scriptPath = $path;
        }
    }
}