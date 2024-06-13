<?php

namespace API {
    class ValidateSchema extends RouteGroup {
        public function __construct() {
            parent::__construct("validateSchema", "\AuditCongress\Enviroment");
            $this->addCustomRoute(new ValidateSchemaRoute());
        }
    }
    class ValidateSchemaRoute extends Route {
        public function __construct() {
            parent::__construct("\AuditCongress\Enviroment", "getDatabaseSchema");
        }
        public function fetchResult() {
            $schema = $this->getCallableFunction()();
            $enforcer = new \MySqlConnector\SchemaEnforcer($schema);
            $enforcer->enforceSchema();
            $operations = $enforcer::getDBOperationsList();
            $result = array("valid" => true, "operations" => $operations);
            return $result;
        }
    }
}

?>