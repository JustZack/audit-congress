<?php

namespace API {
    abstract class RouteGroup {
        public $baseRoute;
        public $routeBaseClass;
        public function __construct($baseRoute, $routeBaseClass) {
            $this->baseRoute = $baseRoute;
            $this->routeBaseClass = $routeBaseClass;
        }

        public function isRoute($otherRoute) {
            return $this->baseRoute == $otherRoute;
        }

        public function name() { return $this->baseRoute; }

        //All RouteGroups exist as singletons
        public abstract static function getInstance();

        //Fetch all route names used by this group
        public function fetchRouteClassNames() {
            return \Util\Classes::thatExtend($this->routeBaseClass);
        }

        public $runnableClassName = null;
        //Check if any of the routes known by this group can run with the given parameters
        public function canRunAny() {
            $classNames = $this->fetchRouteClassNames();
            foreach ($classNames as $class) {
                if (("$class::canRun")()) {
                    $this->runnableClassName = $class;
                    return true;
                }
            }
            return false;
        }

        //Run whichever API route matches the given parameters
        public function fetchResult() {
            if ($this->runnableClassName == null && !$this->canRunAny()) return null;
            return ("$this->runnableClassName::fetchResult")();
        }
    }
}

?>