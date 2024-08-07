<?php

namespace API {
    class Runner {
        public static ?Pagination $pagination = null;
        public ?Schema $schema = null;

        public function __construct(Schema $apiSchema) {
            $this->schema = $apiSchema;
        }
        
        public function processRequest() {
            $route = Parameters::getIfSet("route");
            if ($route != null) $this->runRoute($route);
            else                self::Error("", "No route provided.");
        }

        private function runRoute($route) {
            //Get all classes that extend RouteGroups
            if ($this->schema->hasRoute($route)) {
                $group = $this->schema->getRoute($route);
                self::runRouteGroup($group);
            } else self::NotFound($route);
        }

        private static function runRouteGroup(RouteGroup $routeGroup) {
            $result = null;
            $route = $routeGroup->name();

            try {
                if ($routeGroup->canRunAny()) $result = $routeGroup->fetchResult();
                if ($result === null) self::NotFound($route);
                else self::Success($route, $routeGroup->runnableClassName, $result);    
            } catch (\Cache\WaitingException $cacheException) {
                self::Waiting($route, $routeGroup->runnableClassName, "Waiting for cache to finish compiling.");
            } catch (\API\Exception $exception) {
                self::Error($route, $exception->getMessage());
            }
        }



        public static function getPagination() : Pagination {
            if (self::$pagination == null) {
                $page = Parameters::getIfSet("page", "int");
                $pageSize = Parameters::getIfSet("pageSize", "int");
                $offset = Parameters::getIfSet("offset", "int");
                if ($offset != null) self::$pagination = Pagination::getFromOffset($offset, $pageSize);
                else self::$pagination = Pagination::getFromPage($page, $pageSize);
            } 
            
            return self::$pagination;
        }



        //Actually print the response from the API
        private static function Return($route, $data) {
            $data["request"]["route"] = $route;
            $data["request"]["parameters"] = Parameters::getAll();
            $data["request"]["pagination"] = self::getPagination()->toArray();
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

        //API Success, but waiting for the operation to be available
        public static function Waiting($route, $action, $message) {
            $data = ["request" => array()];
            $data["request"]["status"] = "Waiting";
            $data["request"]["action"] = $action;
            $data["request"]["message"] = $message;
            $data[$route] = [];
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
    }
}

?>