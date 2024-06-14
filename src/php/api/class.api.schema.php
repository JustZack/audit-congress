<?php

namespace API {
    class Schema extends \Util\Schema {
        private $routes = array();

        public function __construct($rawSchemaJson) {
            parent::__construct($rawSchemaJson);
            foreach ($this->raw()["groups"] as $group) 
                $this->routes[$group["name"]] = new RouteGroupSchema($group);
        }

        public function getVersion() { return $this->raw()["version"]; }
        
        public function getRoutes() { return $this->routes; }

        public function listRoutes() { return array_keys($this->routes); }

        public function getRoute($routeName) : RouteGroupSchema { return $this->routes[$routeName]; }

        public function hasRoute($routeName) { return array_key_exists($routeName, $this->routes); }
    }
}

?>