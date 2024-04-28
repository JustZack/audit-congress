<?php

namespace API {
    class Runner {
        //Actually print the response from the API
        private static function Return($route, $data) {
            $data["request"]["route"] = $route;
            $data["request"]["parameters"] = Parameters::getAll();
            header('Content-Type: application/json');
            print_r(json_encode($data));
        }

        //API Success
        public static function Success($route, $action, $result) {
            $data = ["request" => array()];
            $data["request"]["status"] = "Success";
            $data["request"]["action"] = $action;
            $data[$route] = $result;
            self::Return($route, $data);
        }

        //API Error
        public static function Error($route, $error) {
            $data = ["request" => array()];
            $data["request"]["status"] = "error";
            $data["request"]["message"] = $error;
            self::Return($route, $data);
        }

        //API Not found
        public static function NotFound($route) {
            $data = ["request" => array()];
            $data["request"]["status"] = "not found";
            if (strlen($route) > 0) $data["request"]["message"] = "Unknown route: $route";
            else $data["request"]["message"] = "No route provided";
            self::Return($route, $data);
        }

        private static function runRouteGroup(RouteGroup $routeGroup) {
            $result = null;
            if ($routeGroup->canRunAny()) $result = $routeGroup->fetchResult();

            $route = $routeGroup->name();
            $action = $routeGroup->runnableClassName;
            if ($result === null) self::NotFound($route);
            else self::Success($route, $action, $result);
        }

        public static function processRequest() {
            $route = Parameters::getIfSet("route");
            if ($route != null) {
                $routeGroups = \Util\Classes::thatExtend("\API\RouteGroup");
                foreach ($routeGroups as $group) {
                    $instance = ("$group::getInstance")();
                    if ($instance->isRoute($route)) {
                        self::runRouteGroup($instance);
                        return;
                    }
                }
            }
            self::NotFound($route);
        }
    }
}

?>