<?php

namespace API {
    class RouteGroupSchema extends \Util\Schema {
        private RouteGroup $group;
        public function __construct($rawSchema) {
            parent::__construct($rawSchema);
            $raw = $this->raw();
            $this->group = new RouteGroup($this->getName(), $raw["class"]);
            foreach ($raw["routes"] as $route) {
                $required = array_key_exists("required", $route) ? $route["required"] : [];
                $optional = array_key_exists("optional", $route) ? $route["optional"] : [];
                $this->group->addRoute($route["function"], $required, $optional);
            }
        }

        public function getRouteGroup() { return $this->group; }
    }
}

?>