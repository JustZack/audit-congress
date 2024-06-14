<?php

namespace AuditCongress {

    class AuditCongressRow extends \MySqlConnector\SqlRow {

        public static function getTableColumns($tableName) {
            return \AuditCongress\Environment::getDatabaseSchema()->getTable($tableName)->getColumnNames();
        }

        public function mergeColumns($otherColumns) {
            return array_merge($this->getColumns(), $otherColumns);
        }

        public function mergeValues($otherValues) {
            return array_merge($this->getValues(), $otherValues);
        }
    }
}

?>