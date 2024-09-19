<?php

namespace MySqlConnector {
    abstract class QueryWrapper extends QueryOptions implements IParameterizedItem {
        protected Table $table;

        public function __construct($tableName) {
            $this->table = new Table($tableName);
            parent::__construct();
        }

        public function getAsRow() { return SqlRow::fromColsAndVals($this->getColumns(), $this->getValues()); }

        public function countInDB() { return $this->table->count($this->whereClause()); }

        public function selectFromDB() { return $this->table->selectObject($this); }

        public function deleteFromDb() { return $this->table->delete($this->whereClause()); }


        public function truncate() { $this->table->truncate(); }

        public function queueInsert(SqlRow $row) { $this->table->queueInsert(new InsertGroup($row)); }
        public function commitInsert() { return $this->table->commitInsert(); }

        public function insertIntoDB() { return $this->table->insert(new InsertGroup($this->getAsRow())); }

        public function updateInDb() { return $this->table->update(new UpdateGroup($this->getAsRow()), $this->whereClause()); }

        public function getQueryString($withValues = false) {
            $sql = "";
            foreach ($this->getJoins() as $join) 
                $sql .= $join->getQueryString();
            $sql .= $this->where->getQueryString();
            return $sql;
        }
        public function getOrderedParameters() {
            $params = array();
            foreach ($this->getJoins() as $join) 
                $params = array_merge($params, $join->getOrderedParameters());
            $params = array_merge($params, $this->where->getOrderedParameters());
            return $params;
        }

        public function hasAnyParameters() {
            foreach ($this->getJoins() as $join) if ($join->hasAnyConditions()) return true;
            return $this->where->hasAnyConditions();
        }

        public function getOrderedTypes() {
            $types = "";
            foreach ($this->getJoins() as $join) $types .= $join->getOrderedTypes();
            $types .= $this->where->getOrderedTypes();
            return $types;
        }
   


        //Return a condtion for this sql object with the set boolean operator and '=' or 'like'
        public function whereClause() : WhereClause { return $this->where; }
    }
}

?>
