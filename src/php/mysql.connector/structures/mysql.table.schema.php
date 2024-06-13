<?php

namespace MySqlConnector {

    //Contains the expected column & index schema
    class TableSchema {
        private 
            $rawSchema,
            $name;
        private Columns $columns;
        private Indexes $indexes;

        public function __construct($rawSchema) {
            $this->rawSchema = $rawSchema;
            $this->name = $this->rawSchema["name"];
            if (array_key_exists("columns", $this->rawSchema))
                $this->setColumns($this->rawSchema["columns"]);
            else $this->columns = new Columns([]);
            if (array_key_exists("indexes", $this->rawSchema))
                $this->setIndexes($this->rawSchema["indexes"]);
            else $this->indexes = new Indexes([]);
        }
        //Given the raw column schema, build a Columns object
        private function setColumns($columnSchema) {
            $columnsInDescribeFormat = array();
            foreach ($columnSchema as $name=>$data) {
                $primary = isset($data["primary"]) ? "PRI" : "";
                $extra = isset($data["extra"]) ? $data["extra"] : "";
                $column = array($name, $data["type"], $data["null"], $primary, "", $extra);
                array_push($columnsInDescribeFormat, $column);
            }
            $this->columns = new Columns($columnsInDescribeFormat);
        }
        //Given the raw index schema, build an Indexes object
        private function setIndexes($indexSchema) {
            $IndexesInDescribeFormat = array();
            foreach ($indexSchema as $name=>$indexes) {
                for ($i = 0;$i < count($indexes);$i++) {
                    $index = array(1 => 1, 2 => $name, 4 => $indexes[$i], 
                                   5 => "A", 6 => 0, 10 => "BTREE", 13 => "YES");
                    array_push($IndexesInDescribeFormat, $index);
                }
            }
            $this->indexes = new Indexes($IndexesInDescribeFormat);
        }

        public function getName() { return $this->name; }

        public function getColumns() : Columns { return $this->columns; }

        public function getColumnNames() { return array_keys($this->columns->namesAndTypes()); }

        public function getIndexes() : Indexes { return $this->indexes; }
    }
}

?>