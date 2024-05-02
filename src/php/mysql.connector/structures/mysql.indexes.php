<?php

namespace MySqlConnector {
    class Indexes extends CompareableSet {
        public function __construct($indexesArr) {
            foreach ($indexesArr as $indexRow) $this->handleAddIndex(new Index($indexRow));
        }

        public function handleAddIndex(Index $index) {
            //If the index described by the given row already exists
            if ($this->has($index->name)) //Append it to the existing one (this is a multirow index)
                $this->get($index->name)->addColumn($index->columns[0]);
            else if (!$index->isPrimary()) //Otherwise only add non primary key indexes
                $this->add($index);
        }
    }

    class Index extends CompareableObject {
        public
            $nonUnique,
            $columns = array(),
            $collation,
            $cardinality,
            $type,
            $visible;
        public function __construct($indexRow) {
            $this->nonUnique = $indexRow[1];
            $this->name = $indexRow[2];
            $this->addColumn($indexRow[4]);
            $this->collation = $indexRow[5];
            $this->cardinality = $indexRow[6];
            $this->type = $indexRow[10];
            $this->visible = $indexRow[13];
        }

        public function addColumn($column) {
            array_push($this->columns, strtolower($column));
        }

        public function isPrimary() {
            return $this->name == "PRIMARY";
        }

        public function matches($other) {
            if ($other == null) return false;
            else return $this->columns() == $other->columns();
        }

        public function columns() {
            return sprintf("(%s)", implode(",", $this->columns));
        }
    }
}

?>