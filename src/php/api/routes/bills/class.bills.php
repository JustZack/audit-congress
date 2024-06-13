<?php

namespace API {
    class Bills extends RouteGroup {
        public function __construct() {
            parent::__construct("bills", "\AuditCongress\Bills");
            $this->addRoute("getById", ["id" => "string"]);
            $this->addRoute("getByBioguideId", ["bioguideId" => "string"]);
            $this->addRoute("getByFilter", [], ["congress" => "string", "type" => "string", "number" => "string", "title" => "string", "sort" => "array"]);
        }
    }
}

?>