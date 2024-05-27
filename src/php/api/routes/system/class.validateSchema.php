<?php

namespace API {
    class ValidateSchema extends RouteGroup {
        public function __construct() {
            parent::__construct("validateSchema", "\API\ValidateSchemaRoute");
        }
    }

    //Declare MemberRoute so that all MemberRoutes can be identified as one of its children
    abstract class ValidateSchemaRoute extends Route { }

    class ValidateSchemaSingle extends ValidateSchemaRoute {
        public static function fetchResult() {
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