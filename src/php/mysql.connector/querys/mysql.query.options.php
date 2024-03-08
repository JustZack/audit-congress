<?php

namespace MySqlConnector {
    abstract class QueryOptions {
        private
            $selectColumns = array("*"),
            $searchColumns = array(), 
            $searchValues = array(),  
            $columns = array(), 
            $values = array(),
            $join = null,
            $orderBy = null,
            $groupBy = null,
            $limit = null;


        //Get the columns used when selecting this object
        public function getSelectColumns() { return $this->selectColumns; }
        //Set the columns used when selecting  this object
        public function setSelectColumns(array $newColumns) { return $this->selectColumns = $newColumns; }

        //Get the columns used when searching for this object
        public function getSearchColumns() { return $this->searchColumns; }
        //Set the columns used when searching for this object
        public function setSearchColumns(array $newColumns) { return $this->searchColumns = $newColumns; }

        //Get the values provided to this object
        public function getSearchValues() { return $this->searchValues; }
        //Set the values used by this object
        public function setSearchValues(array $newValues) { return $this->searchValues = $newValues; }

        //Get the columns provided to this object
        public function getColumns() { return $this->columns; }
        //Set the columns used by this object (for setting or updating values)
        public function setColumns(array $newColumns) { return $this->columns = $newColumns; }

        //Get the values provided to this object
        public function getValues() { return $this->values; }
        //Set the values used by this object
        public function setValues(array $newValues) { return $this->values = $newValues; }

        //Get the order by string provided to this object
        public function getOrderBy() { return $this->orderBy; }
        //Set the order by string used by this object
        public function setOrderBy($newOrderByColumns, $isAsc = true) { 
            $orderList = QueryBuilder::buildItemList($newOrderByColumns, false, "");
            $this->orderBy = "$orderList ".($isAsc?"ASC":"DESC");
        }

        //Get the group by string provided to this object
        public function getGroupBy() { return $this->groupBy; }
        //Set the group by string used by this object
        public function setGroupBy($newGroupByColumns) { 
            $this->groupBy = QueryBuilder::buildItemList($newGroupByColumns, false, "");
        }

        //Get the row limit provided to this object
        public function getLimit() { return $this->limit; }
        //Set the row limit used by this object
        public function setLimit($newLimit) { $this->limit = $newLimit; }

        public function getJoin() { return $this->join; }

        public function setJoin($onTable, $onTableColumns, $thisTableColumns) {
            $format = "%s ON %s";
            $joinCondition = QueryBuilder::buildOnCondition($onTableColumns, $thisTableColumns, "=", "AND");
            $this->join = sprintf($format, $onTable, $joinCondition);
        }
    }
}

?>
