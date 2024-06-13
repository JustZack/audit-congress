<?php

namespace API {
    class Bills extends RouteGroup {
        public function __construct() {
            parent::__construct("bills", "\AuditCongress\Bills");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBioguideId", ["bioguideId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "int", "type" => "string", "number" => "int", "title" => "string", "sort" => "array"]);
        }
    }
}

?>