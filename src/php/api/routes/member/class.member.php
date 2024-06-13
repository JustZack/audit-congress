<?php

namespace API {
    class Member extends RouteGroup {
        public function __construct() {
            parent::__construct("members", "\AuditCongress\Members");
            $this->addRoute("getByBioguideId", ["id" => "string"]);
            $this->addRoute("getByAnyName", ["name" => "string"], ["current" => "bool"]);
            $this->addRoute("getByFilter", [], ["state" => "string", "type" => "string", "party" => "string", "gender" => "string", "current" => "bool"]);
        }
    }
}

?>