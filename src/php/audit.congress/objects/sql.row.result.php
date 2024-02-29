<?php

namespace AuditCongress {
    class SqlRow {
        use setFieldsFromObject;

        public function __construct($rowAssocArray) {
            $this->setFieldsFromObject($rowAssocArray);    
        }
    }
}

?>