<?php

namespace API {
    abstract class RouteGroup {
        public $baseRoute;
        public function __construct($baseRoute) {
            $this->baseRoute = $baseRoute;
        }

        public function isRoute($otherRoute) {
            return $this->baseRoute == $otherRoute;
        }

        public function name() { return $this->baseRoute; }

        //All RouteGroups exist as singletons
        public abstract static function getInstance();

        //Fetch all route names used by this group
        public abstract static function fetchRouteClassNames();

        public $runnableClassName = null;
        //Check if any of the routes known by this group can run with the given parameters
        public function canRunAny() {
            $ns = __NAMESPACE__;
            $classNames = static::fetchRouteClassNames();
            foreach ($classNames as $class) {
                if (("$ns\\$class::canRun")()) {
                    $this->runnableClassName = $class;
                    return true;
                }
            }
            return false;
        }

        //Run whichever API route matches the given parameters
        public function fetchResult() {
            if ($this->runnableClassName == null && !$this->canRunAny()) return null;
            $className = __NAMESPACE__."\\".$this->runnableClassName;
            return ("$className::fetchResult")();
        }
    }
}

?>