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

        //Check if any of the routes known by this group can run with the given parameters
        public abstract function canRunAny($parameters);
        //Run whichever API route matches the given parameters
        public abstract function fetchResult($parameters);
    }
}

?>