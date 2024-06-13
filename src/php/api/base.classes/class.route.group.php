<?php

namespace API {
    abstract class RouteGroup {
        public 
            $baseRoute,
            $routeBaseClass,
            $routes = array();
        public function __construct($baseRoute, $routeBaseClass) {
            $this->baseRoute = $baseRoute;
            $this->routeBaseClass = $routeBaseClass;
        }

        public function isRoute($otherRoute) {
            return $this->baseRoute == $otherRoute;
        }

        public function name() { return $this->baseRoute; }

        public function addRoute($functionName, $requiredParams = [], $optionalParams = []) {
            $theRoute = new Route($this->routeBaseClass, $functionName, $requiredParams, $optionalParams);
            $this->addCustomRoute($theRoute);
        }
        public function addCustomRoute($theRoute) {
            array_push($this->routes, $theRoute);
        }
        public function getRoutes() { return $this->routes; }

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
            $currentRunnableObject = null;
            
            $routes = $this->getRoutes();
            foreach ($routes as $route) {
                $paramCount = count($route->parameters());
                if ($route->canRun() && $paramCount > $currentRunnableParameters) {
                    $currentRunnable = $route->getCallableFunction();
                    $currentRunnableObject = $route;
                    $currentRunnableParameters = $paramCount;
                }
            }

            if ($currentRunnableObject == null) return false;
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