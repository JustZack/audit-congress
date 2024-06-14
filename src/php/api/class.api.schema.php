<?php

namespace API {
    class Schema extends \Util\Schema {
        private $routes = array();

        public function __construct($rawSchemaJson) {
            parent::__construct($rawSchemaJson);
            foreach ($this->raw()["groups"] as $group) 
                $this->routes[$group["name"]] = $this->createRouteGroup($group);
        }

        private function createRouteGroup($group) {
            $routeGroup = new RouteGroup($group["name"], $group["class"]);
            foreach ($group["routes"] as $route) {
                $required = array_key_exists("required", $route) ? $route["required"] : [];
                $optional = array_key_exists("optional", $route) ? $route["optional"] : [];
                $routeGroup->addRoute($route["function"], $required, $optional);
            }
            return $routeGroup;
        }

        public function getVersion() { return $this->raw()["version"]; }
        
        public function getRoutes() { return $this->routes; }

        public function listRoutes() { return array_keys($this->routes); }

        public function getRoute($routeName) : RouteGroup { return $this->routes[$routeName]; }

        public function hasRoute($routeName) { return array_key_exists($routeName, $this->routes); }
    }
}

?>