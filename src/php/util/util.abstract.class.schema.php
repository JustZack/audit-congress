<?php

namespace Util {
    
    //General base class for any schema related objects
    abstract class Schema {
        private $rawSchema;

        public function __construct($rawSchemaJSON) {
            $this->rawSchema = $rawSchemaJSON;
        }

        public function raw() { return $this->rawSchema; }

        public function getName() { return $this->raw()["name"]; }
    }
}

?>