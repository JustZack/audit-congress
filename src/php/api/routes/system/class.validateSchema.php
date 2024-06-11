<?php

namespace API {
    class ValidateSchema extends RouteGroup {
        public function __construct() {
            parent::__construct("validateSchema", "\API\ValidateSchemaRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ValidateSchemaRoute extends Route {
        public function __construct($functionName, $parameters) {
            parent::__construct("\MySqlConnector\SchemaEnforcer", $functionName, $parameters);
        }
    }

    class ValidateSchemaSingle extends ValidateSchemaRoute {
        public function __construct() {
            parent::__construct("", []);
        }
        public function fetchResult() {
            $schema = \AuditCongress\Enviroment::getDatabaseSchema();
            $enforcer = new \MySqlConnector\SchemaEnforcer($schema);
            $enforcer->enforceSchema();
            $operations = $enforcer::getDBOperationsList();
            $result = array("valid" => true, "operations" => $operations);
            return $result;
        }
    }
}

?>