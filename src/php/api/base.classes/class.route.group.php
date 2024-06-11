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

        //Fetch all route names used by this group
        public function fetchRouteClassNames() {
            return \Util\Classes::thatExtend($this->routeBaseClass);
        }

        public 
            $runnableClassName = null,
            $runnableObject = null;
        //Check if any of the routes known by this group can run with the given parameters
        public function canRunAny() {
            $currentRunnable = null;
            $currentRunnableParameters = -1;
            $classNames = $this->fetchRouteClassNames();

            foreach ($classNames as $class) {
                $routeObj = new $class();
                $nParams = count($routeObj->parameters());
                if ($routeObj->canRun() && $nParams > $currentRunnableParameters) {
                    $currentRunnable = $class;
                    $currentRunnableObject = $routeObj;
                    $currentRunnableParameters = $nParams;
                }
            }

            if ($currentRunnable == null) return false;
            else {
                $this->runnableClassName = $currentRunnable;
                $this->runnableObject = $currentRunnableObject;
            }
            return true;
        }

        //Run whichever API route matches the given parameters
        public function fetchResult() {
            if ($this->runnableObject == null && !$this->canRunAny()) return null;
            return $this->runnableObject->fetchResult();
        }
    }
}

?>