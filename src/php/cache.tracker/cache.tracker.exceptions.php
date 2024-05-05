<?php

namespace Cache {

    class WaitingException extends \Exception {
        public ?Tracker $tracker = null;
        public $message = null;
        public function __construct(Tracker $cacheTracker) {
            $this->tracker = $cacheTracker;
            parent::__construct("Waiting for cache update.");
        }
    }
}

?>