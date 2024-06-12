<?php

namespace AuditCongress {

    class BillSubjectRow extends AbstractBillItemIndexRow {
        public $subject;
    
        public function getColumns() { return self::getTableColumns("BillSubjects"); }

        public function getValues() {
            return parent::mergeValues([$this->subject]);
        }
    }
}

?>