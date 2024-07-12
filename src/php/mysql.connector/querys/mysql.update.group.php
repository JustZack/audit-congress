<?php

namespace MySqlConnector {
    class UpdateGroup extends InsertGroup implements IParameterizedItem {
        
        public function getQueryString($withValues = false) {
            $set = array();
            foreach ($this->columns() as $col) array_push($set, "`$col` = ?");
            return QueryBuilder::buildSetList($set);
        }
    }
}

?>