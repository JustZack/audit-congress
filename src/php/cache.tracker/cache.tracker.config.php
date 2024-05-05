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

            $settings = null,
            $cacheRow = null;
        
        public function __construct($name, $settings) {
            $this->settings = $settings;

            $this->name = $name;
            $this->status = $settings["status"];
            $this->scriptPath = isset($settings["scriptPath"]) ? $settings["scriptPath"] : null;
            $this->scriptRunner = isset($settings["scriptRunner"]) ? $settings["scriptRunner"] : null;

            $dailyUpdateTimes = isset($settings["updateTimesIn24HrUTC"]) ? $settings["updateTimesIn24HrUTC"] : null;
            $updateInterval = isset($settings["updateIntervalInHours"]) ? $settings["updateIntervalInHours"] : null;
            if (isset($dailyUpdateTimes) && count($dailyUpdateTimes) > 0) {
                $this->updateIntervalInHours = false;
                $this->updateTimesIn24HrUTC = $dailyUpdateTimes;
            } else if (isset($updateInterval)) {
                $this->updateIntervalInHours = $updateInterval;
                $this->updateTimesIn24HrUTC = false;
            } else {
                $this->updateIntervalInHours = false;
                $this->updateTimesIn24HrUTC = false;
            }
        }

        public function isset() { return $this->getRow() != null; }

        protected function invalidate() { $this->cacheRow = null; }

        public function refresh() {
            $this->cacheRow = \Cache\TrackerQuery::getCacheStatus($this->name);
        }

        private function getRow() {
            if ($this->cacheRow == null) $this->refresh();
            return $this->cacheRow;
        }

        protected function getValue($column) {
            $row = $this->getRow();
            if ($row != null) return $row[$column];
            else              return false;
        }


        public function settings() { return $this->settings; }
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