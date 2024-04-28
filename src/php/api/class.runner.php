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
        public static function Success($route, $result) {
            $data = ["request" => array()];
            $data["request"]["status"] = "Success";
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
            //$route = $routeGroup->name();
            $route = $routeGroup->runnableClassName;
            $result = null;

            if ($routeGroup->canRunAny()) $result = $routeGroup->fetchResult();

            if ($result == null) self::NotFound($route);
            else self::Success($route, $result);
        }

        //Run the member route
        public static function runMember() {
            self::runRouteGroup(\API\Member::getInstance());
        }
    }
}

?>