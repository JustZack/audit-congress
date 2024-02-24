<?php

namespace AuditCongress {

    class SchemaBuilder {
        public $schema = null;
        public function __construct($schemaFilePath = AUDITCONGRESS_FOLDER."\schema\schema.json") {
            $this->schema = json_decode(file_get_contents($schemaFilePath), true);
            $this->enforceSchema();
        }

        public function enforceSchema() {
            foreach ($this->schema["tables"] as $tableSchema) {
                $table = new \MySqlConnector\Table($tableSchema["name"]);
                //If the table doesnt exist, we can simply create the table
                if (!$table->exists()) {
                    $table->create($tableSchema["columns"]);
                } 
                //Otherwise we need to ensure the schema is being followed by this table
                else {
                    //TODO: Enforce schema
                }
            }
        }
    }
}

?>